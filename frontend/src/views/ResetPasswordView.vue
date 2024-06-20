<script setup>
import { computed, onMounted, ref } from 'vue'
import { customFetch } from '../utils'
import { useAuthStore } from '@/stores/auth'
import router from '@/router'
import Swal from 'sweetalert2'

const token = ref(null)
const isLoading = ref(false)
const password = ref('')
const confirmationPassword = ref('')
const errorMessage = ref(null)
const showPassword = ref(false)

async function requestPasswordChange() {
  isLoading.value = true

  try {
    const { response, data } = await customFetch('/user/forgot-password', 'POST', {
      email: email.value
    })
    if (response.ok) {
      errorMessage.value = null
      Swal.fire({
        title: 'Success',
        text: data.message,
        icon: 'success'
      })
    } else {
      errorMessage.value = data.message ?? 'An error occurred. Please try again'
    }
  } catch (error) {
    console.error(error)
    errorMessage.value = { message: error.message }
  }

  isLoading.value = false
}

const passwordToggleClass = computed(() => ({
  'bi-eye': showPassword.value,
  'bi-eye-slash': !showPassword.value
}))

onMounted(() => {
  const resetPasswordToken = new URLSearchParams(location.search).get('token')
  if (!resetPasswordToken) {
    Swal.fire({
      title: `No token given`,
      toast: true,
      timer: 2000,
      position: 'top-end',
      showConfirmButton: false,
      icon: 'error'
    })
    router.push({ name: 'home' })
  }

  token.value = resetPasswordToken
})
</script>

<template>
  <div class="section">
    <div class="body">
      <div class="heading">
        <h4 class="title">Reset Password</h4>
        <div class="caption">Enter your new password below</div>
      </div>

      <form @submit.prevent="requestPasswordChange">
        <div class="m-form-group">
          <label for="passwordInput" class="form-label">Password</label>
          <div class="input-group">
            <input
              :type="showPassword ? 'text' : 'password'"
              id="passwordInput"
              v-model="password"
              class="form-control"
              required
            />
            <span class="input-group-text password-toggle" @click="showPassword = !showPassword">
              <i class="bi" :class="passwordToggleClass" aria-hidden="true"></i>
            </span>
          </div>
        </div>

        <div class="m-form-group">
          <label for="confirmPasswordInput" class="form-label">Confirm Password</label>
          <div class="input-group">
            <input
              :type="showPassword ? 'text' : 'password'"
              id="confirmPasswordInput"
              v-model="confirmationPassword"
              class="form-control"
              required
            />
            <span class="input-group-text password-toggle" @click="showPassword = !showPassword">
              <i class="bi" :class="passwordToggleClass" aria-hidden="true"></i>
            </span>
          </div>
          <div v-if="password !== confirmationPassword">
            <span class="error"> Passwords do not match. </span>
          </div>
        </div>

        <button type="submit" class="btn btn-primary" :disabled="isLoading">
          <span v-show="!isLoading">Submit</span>
          <div v-show="isLoading" class="spinner-grow" role="status"></div>
        </button>
      </form>
    </div>
  </div>
</template>

<style scoped>
.section {
  display: flex;
  align-items: center;
  justify-content: center;

  margin-top: 3em;
}

.heading {
  width: 100%;
  text-align: center;
  margin-bottom: 1.5em;

  display: flex;
  flex-direction: column;
}

.caption {
  font-size: 0.8em;
}

.body {
  width: 500px;
  border: 1px solid silver;
  border-radius: 3%;
  padding: 1em;
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

.error,
.general-error {
  color: red;
  font-size: 0.8em;
}

.general-error {
  padding-top: 0.2em;
  padding-left: 0.5em;
  padding-bottom: 0.5em;
}

.password-toggle {
  cursor: pointer;
}

.password-toggle:hover {
  filter: saturate(0.5);
}
</style>
