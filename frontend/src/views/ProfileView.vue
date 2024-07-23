<script setup>
import { customFetch } from '@/utils'
import { ref, onMounted, watch } from 'vue'
import { useAuthStore } from '@/stores/auth'
import FeedbackList from '@/components/FeedbackList.vue'
import router from '@/router'
import { useRoute } from 'vue-router'
import PasswordField from '@/components/PasswordField.vue'
import AvatarInputField from '@/components/AvatarInputField.vue'
import Swal from 'sweetalert2'

const baseUrl = import.meta.env.VITE_BACKEND_BASE_URL
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

const avatarFileInput = ref(null)
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

const defaultAvatarUrl = `${import.meta.env.VITE_BACKEND_BASE_URL}/storage/blank-avatar.jpg`

authStore.$subscribe((mutation, state) => {
  if ((userId === null || userId === undefined || userId === '') && state.userId) {
    userId = state.userId
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
  console.log(data)

  if (screen.width <= 600) {
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

  const formData = new FormData()
  for (const [key, value] of Object.entries(edittedUser.value)) {
    formData.append(key, value)
  }

  const { response, data } = await customFetch(`/user/update`, 'POST', formData, false)

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
  if (newPassword.value !== newPasswordConfirmation.value) {
    return
  }

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

function setNewAvatar(event) {
  const file = event.target.files[0]
  edittedUser.value.avatar = file
  edittedUser.value.avatarUrl = URL.createObjectURL(file)
}

function removeSelectedAvatar() {
  edittedUser.value.avatar = null
  edittedUser.value.avatarUrl = null
}

async function deleteAccount() {
  const res = await Swal.fire({
    title: 'Delete User',
    text: 'Are you sure you want to delete this user account?',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#3085d6',
    cancelButtonColor: '#d33',
    confirmButtonText: 'Yes'
  })
  if (!res.isConfirmed) {
    return
  }

  const { response, data } = await customFetch(`/user/${userId}`, 'DELETE')

  Swal.fire({
    text: response.message,
    toast: true,
    timer: 2000,
    position: 'top-end',
    showConfirmButton: false,
    icon: response.ok ? 'success' : 'error'
  })

  router.push({ name: 'home' })
  if (userId === authStore.userId) {
    authStore.logout()
  }
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
      <img
        class="avatar"
        :src="user.avatar_url ? `${baseUrl}/${user.avatar_url}` : defaultAvatarUrl"
        alt="Avatar"
      />
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
      ></i>

      <avatar-input-field
        :currentAvatarUrl="user.avatar_url"
        :newAvatarUrl="edittedUser.avatarUrl"
        @on-avatar-selected="setNewAvatar"
        @on-avatar-removed="removeSelectedAvatar"
      />

      <div class="m-form-group">
        <label for="nameInput" class="form-label">Name</label>
        <input
          type="text"
          id="nameInput"
          v-model="edittedUser.name"
          class="form-control"
          :placeholder="user.name"
        />
      </div>

      <div class="m-form-group">
        <label for="emailInput" class="form-label">Email address</label>
        <input
          type="email"
          id="emailInput"
          v-model="edittedUser.email"
          class="form-control"
          :placeholder="user.email"
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

      <span v-show="newPassword !== newPasswordConfirmation" class="error">
        Passwords do not match.
      </span>

      <button type="submit" class="btn" :disabled="isUpdatingPassword">
        <span v-show="!isUpdatingPassword">Update Password</span>
        <div v-show="isUpdatingPassword" class="spinner-grow" role="status"></div>
      </button>
    </form>

    <div v-if="user && isEditing" class="user-info bordered">
      <button class="btn btn-danger" @click="deleteAccount">Delete Account</button>
    </div>

    <div v-show="isLoading && feedbackData.data.length === 0" class="spinner-border center" />

    <div v-show="feedbackData.data.length === 0 && !isLoading && isError">
      <span>An error occurred while getting feedback.</span>
    </div>

    <feedback-list
      :feedbackData="feedbackData"
      :isLoading="isLoading"
      emptyMessage="No feedback added yet."
      @page-no-clicked="navigateToPage($event)"
    />
  </div>
</template>

<style scoped>
:root {
  --input-avi-background-color: #7e7c7c;
}

a {
  text-decoration: none;
}

.nav-item {
  cursor: pointer;
}

.body,
.user-info {
  display: flex;
  flex-direction: column;
  justify-content: space-around;
  align-items: center;

  row-gap: 0.5em;

  padding-left: 0.5em;
  padding-right: 0.5em;
}

.feedback-list {
  margin: 1em;
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

.center {
  margin: 10em;
}

.btn:hover,
.btn:focus {
  filter: saturate(0.5);
}

.avatar {
  width: 100px;
  height: 100px;
  border-radius: 50%;
}

.error {
  color: red;
  font-size: 0.8em;
  padding-bottom: 0.5em;
}

@media screen and (max-width: 500px) {
  .user-info {
    width: 100%;
  }
}
</style>
