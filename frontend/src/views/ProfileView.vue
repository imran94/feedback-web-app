<script setup>
import { customFetch } from '@/utils'
import { ref, onMounted, watch } from 'vue'
import { useAuthStore } from '@/stores/auth'
import FeedbackList from '@/components/FeedbackList.vue'
import router from '@/router'
import { useRoute } from 'vue-router'
import PasswordField from '@/components/PasswordField.vue'
import Swal from 'sweetalert2'

const route = useRoute()
const authStore = useAuthStore()
let userId = route.params.id !== '' ? route.params.id : authStore.userId ?? ''

const isLoading = ref(true)
const isLoadingUser = ref(true)
const userErrorMessage = ref('')
const isUserError = ref(false)
const isError = ref(false)
const isEditing = ref(false)
const isUpdatingUser = ref(false)
const isUpdatingPassword = ref(false)

const user = ref(null)
const edittedUser = ref({
  email: '',
  name: ''
})
const newPassword = ref('')
const newPasswordConfirmation = ref('')
const feedbackData = ref({
  data: [],
  current_page: 1,
  rows: 0,
  total: 0,
  per_page: 15
})

authStore.$subscribe((mutation, state) => {
  if ((userId === null || userId === undefined || userId === '') && state.userId) {
    userId = state.userId
    console.log('updated userId to ', userId)
    fetchData()
  }

  if (route.params.id === '' && !state.isAuth && !state.isLoadingUser) {
    router.push({ name: 'home' })
  }
})

watch(
  () => route.params.id,
  (newId, oldId) => {
    userId = newId
    user.value = null
    feedbackData.value = {
      data: [],
      current_page: 1,
      rows: 0,
      total: 0,
      per_page: 15
    }
    fetchData()
  }
)

onMounted(() => {
  if (userId !== '') {
    fetchData()
  }
})

function fetchData() {
  fetchInfo()
  fetchPosts()
}

async function fetchInfo() {
  isLoadingUser.value = true

  const { response, data } = await customFetch(`/user/${userId}`)

  if (!response.ok) {
    userErrorMessage.value = response.status === 404 ? 'User not found' : data.message
    isUserError.value = true
    isLoadingUser.value = false
    return
  }

  data.createdAt = new Date(data['created_at']).toLocaleDateString()
  user.value = data
  edittedUser.value = {
    name: data.name,
    email: data.email
  }
  isLoadingUser.value = false
}

async function fetchPosts() {
  isLoading.value = true
  isError.value = false

  const { response, data } = await customFetch(
    `/user/${userId}/feedback?page=${feedbackData.value.current_page}&limit=${feedbackData.value.per_page}`
  )
  if (!response.ok) {
    isLoading.value = false
    isError.value = true
    return
  }

  feedbackData.value = data

  if (screen.width <= 600) {
    // Shorten the number of page links to fit screen
    const links = feedbackData.value.links
    const newLinks = []

    let currentPage = feedbackData.value.current_page
    const currentPageIndex = links.findIndex((link) => parseInt(link.label) === currentPage)
    const prevOffset = currentPageIndex === links.length - 2 ? 4 : 2
    for (let i = currentPageIndex - 1; i > 1; i--) {
      newLinks.unshift(links[i])
      if (newLinks.length >= prevOffset) break
    }

    newLinks.push(links[currentPageIndex])

    for (let i = currentPageIndex + 1; i < links.length - 1; i++) {
      newLinks.push(links[i])
      if (newLinks.length >= 5) break
    }

    newLinks.unshift({
      url: links[0].url,
      label: '&laquo;',
      active: links[0].active
    })

    newLinks.push({
      url: links[links.length - 1].url,
      label: '&raquo;',
      active: links[links.length - 1].active
    })

    feedbackData.value.links = newLinks
  }
  isLoading.value = false
}

function navigateToPage(link) {
  if (link.active) return

  if (link.label.includes('&laquo;')) {
    feedbackData.value.current_page--
  } else if (link.label.includes('&raquo;')) {
    feedbackData.value.current_page++
  } else {
    feedbackData.value.current_page = link.label
  }

  fetchPosts()
}

async function updateUser() {
  isUpdatingUser.value = true

  const { response, data } = await customFetch(`/user`, 'PUT', {
    name: edittedUser.value.name,
    email: edittedUser.value.email
  })

  const message = response.ok ? 'Your info has been updated.' : data.message
  Swal.fire({
    title: message,
    toast: true,
    timer: 3000,
    showConfirmButton: false,
    position: 'top-end',
    icon: response.ok ? 'success' : 'error'
  })

  if (response.ok) {
    user.value = data
    isEditing.value = false
  }

  isUpdatingUser.value = false
}

async function updatePassword() {
  isUpdatingPassword.value = true

  const { response, data } = await customFetch(`/user/reset-password`, 'PUT', {
    password: newPassword.value,
    password_confirmation: newPasswordConfirmation.value
  })

  Swal.fire({
    title: data.message,
    toast: true,
    timer: 3000,
    showConfirmButton: false,
    position: 'top-end',
    icon: response.ok ? 'success' : 'error'
  })

  if (response.ok) {
    isEditing.value = false
  }

  isUpdatingPassword.value = false
}
</script>

<template>
  <div class="body">
    <div v-if="user && !isEditing" class="user-info bordered">
      <i
        class="bi bi-pencil clickable edit-icon"
        v-if="user.id === authStore.userId"
        @click="isEditing = true"
      ></i>
      <span>{{ user.name }}</span>
      <span>{{ user.email }}</span>
      <span v-if="user['is_admin']">Admin</span>
      <span>Joined {{ user.createdAt }}</span>
    </div>

    <form v-if="user && isEditing" class="user-info bordered" @submit.prevent="updateUser">
      <i
        class="bi bi-x-lg clickable edit-icon"
        v-if="user.id === authStore.userId"
        @click="isEditing = false"
      />
      <div class="m-form-group">
        <label for="nameInput" class="form-label">Name</label>
        <input
          type="text"
          id="nameInput"
          v-model="edittedUser.name"
          class="form-control"
          required
        />
      </div>

      <div class="m-form-group">
        <label for="emailInput" class="form-label">Email address</label>
        <input
          type="email"
          id="emailInput"
          v-model="edittedUser.email"
          class="form-control"
          placeholder="name@example.com"
          required
        />
      </div>
      <button type="submit" class="btn" :disabled="isUpdatingUser">
        <span v-show="!isUpdatingUser">Update</span>
        <div v-show="isUpdatingUser" class="spinner-grow" role="status"></div>
      </button>
    </form>

    <form v-if="user && isEditing" class="user-info bordered" @submit.prevent="updatePassword">
      <div class="m-form-group">
        <password-field v-model="newPassword" />
      </div>

      <div class="m-form-group">
        <password-field label="Confirm Password" v-model="newPasswordConfirmation" />
      </div>

      <button type="submit" class="btn" :disabled="isUpdatingPassword">
        <span v-show="!isUpdatingPassword">Update Password</span>
        <div v-show="isUpdatingPassword" class="spinner-grow" role="status"></div>
      </button>
    </form>

    <div v-if="user && isEditing" class="user-info bordered">
      <button class="btn btn-danger">Delete Account</button>
    </div>

    <div v-show="isLoading && feedbackData.data.length === 0" class="spinner-border center" />

    <div v-show="feedbackData.data.length === 0 && !isLoading && !isError">
      Looks like you haven't added any feedback.
    </div>

    <div v-show="feedbackData.data.length === 0 && !isLoading && isError">
      <span>An error occurred while getting feedback.</span>
    </div>

    <feedback-list :feedbackData="feedbackData" @page-no-clicked="navigateToPage($event)" />
  </div>
</template>

<style scoped>
a {
  text-decoration: none;
}

.nav-item {
  cursor: pointer;
}

.body {
  width: 100%;
  padding: 1em;
}

.body,
.user-info {
  display: flex;
  flex-flow: column wrap;
  justify-content: space-around;
  align-items: center;

  row-gap: 0.5em;
}

.user-info {
  width: 450px;
  padding: 1em;

  border: 1px solid silver;
  border-radius: 3%;
}

.edit-icon {
  align-self: flex-end;
}

.tab-content {
  padding: 0.5em 1em;
}

.m-card {
  margin: 1em 0.5em;
  padding: 1em;
  box-shadow: 2px 2px 5px 2px rgba(0, 0, 0, 0.4);
  border-radius: 0.5em;

  color: black;
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: flex-start;
}

a.m-card:hover {
  background-color: #ebebeb;
  box-shadow: 5px 5px 10px 5px rgba(0, 0, 0, 0.8);
}

.m-card-subtitle {
  display: inline-flex;
  justify-content: space-between;
}

.m-card-subtitle * {
  padding-right: 5px;
}

.center {
  margin: 10em;
}

.btn {
  box-shadow: 2px 2px 5px 2px rgba(0, 0, 0, 0.4);
  background-color: royalblue;
  color: white;
}

.btn-danger {
  background-color: maroon;
}

.btn:hover,
.btn:focus {
  filter: saturate(0.5);
}
</style>
