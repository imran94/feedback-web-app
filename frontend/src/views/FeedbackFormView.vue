<script setup>
import { computed, onMounted, ref } from 'vue'
import Swal from 'sweetalert2'
import { customFetch, categories } from '../utils'
import router from '@/router'
import { marked } from 'marked'
import Editor from 'primevue/editor'

import { useAuthStore } from '@/stores/auth'
import { useThemeStore } from '@/stores/theme'

const auth = useAuthStore()
const theme = useThemeStore()
const isLoading = ref(false)
const isEditing = ref(false)
const errorMessage = ref({})
const post = ref({
  title: '',
  description: '',
  category: ''
})

auth.$subscribe((mutation, state) => {
  if (!state.isAuth && !state.isLoadingUser) {
    router.push({ name: 'home' })
  }
})

onMounted(() => {
  const feedbackId = location.pathname.split('/')[2]

  if (feedbackId) {
    fetchPost(feedbackId)
  }
})

const darkTheme = computed(() => ({
  'dark-theme': theme.isDarkMode
}))

const convertedDescription = computed(() => {
  return marked.parse(post.value.description)
})

async function fetchPost(postId) {
  isLoading.value = true
  const { response, data } = await customFetch(`/feedback/${postId}`)
  if (response.status === 200) {
    post.value = data
    if (post.value['user_id'] !== auth.userId) {
      Swal.fire({
        title: `You are not authorized to edit this post`,
        toast: true,
        timer: 2000,
        position: 'top-end',
        showConfirmButton: false,
        icon: 'warning'
      })
      router.push({ name: 'feedbackThread', params: { id: postId } })
    }
  }

  isLoading.value = false
}

async function submitFeedback() {
  isLoading.value = true
  const endpoint = `/feedback/${post.value.id ?? ''}`
  const method = post.value.id ? 'PUT' : 'POST'
  try {
    const { response, data } = await customFetch(endpoint, method, post.value)
    if (response.status === 200) {
      const mPost = data
      Swal.fire({
        title: `Feedback successfully ${post.value.id ? 'edited' : 'created'}`,
        toast: true,
        timer: 2000,
        position: 'top-end',
        showConfirmButton: false,
        icon: 'success'
      })
      router.push({ name: 'feedbackThread', params: { id: mPost.id } })
    } else {
      Swal.fire({
        title: 'An error occurred while trying to submit feedback',
        toast: true,
        timer: 2000,
        position: 'top-end',
        showConfirmButton: false
      })
    }
  } catch (error) {
    console.error(error)
    errorMessage.value = { message: error.message }
  }

  isLoading.value = false
}
</script>

<template>
  <div class="section">
    <div class="body">
      <h4 class="title">Feedback Form</h4>

      <form @submit.prevent="submitFeedback">
        <div class="m-form-group">
          <label for="categorySelect" class="form-label">Category</label>
          <select
            id="categorySelect"
            v-model="post.category"
            class="form-select form-select-sm"
            required
            :disabled="isLoading"
          >
            <option disabled value="">Please select one</option>
            <option v-for="category in categories" :key="category" :value="category">
              {{ category }}
            </option>
          </select>
        </div>

        <div class="m-form-group">
          <label for="titleInput" class="form-label">Title</label>
          <input
            v-model="post.title"
            type="text"
            id="titleInput"
            class="form-control"
            maxlength="250"
            required
            :disabled="isLoading"
          />
        </div>

        <div class="m-form-group">
          <label>Description</label>
          <Editor :class="darkTheme" v-model="post.description" editorStyle="height: 320px" />
        </div>

        <div v-show="errorMessage?.message" class="input-error">{{ errorMessage.message }}</div>

        <button type="submit" class="btn btn-primary" :disabled="isLoading">
          <span v-show="!isLoading">
            <span v-if="post.id">Edit</span>
            <span v-else>Submit</span>
          </span>
          <div v-show="isLoading" class="spinner-grow" role="status"></div>
        </button>
      </form>
    </div>
  </div>
</template>

<style scoped>
.body {
  width: 700px;
  max-width: 95%;
  border: 1px solid silver;
  border-radius: 10px;
  padding: 1em;
}

.section {
  display: flex;
  align-items: center;
  justify-content: center;

  margin-top: 3em;
}

.title {
  width: 100%;
  text-align: center;
  margin-bottom: 1.5em;
}

form {
  display: flex;
  flex-flow: column wrap;
  align-items: center;
}

.m-form-group {
  width: 100%;
  margin-bottom: 2em;
}

.input-error {
  color: red;
  font-size: 0.8em;
  padding-top: 0.2em;
  padding-left: 0.5em;
  padding-bottom: 0.5em;
}

.dark-theme {
  color-scheme: dark;
}
</style>
