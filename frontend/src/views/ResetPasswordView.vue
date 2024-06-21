<script setup>
import { computed, onMounted, ref } from 'vue'
import { customFetch } from '../utils'
import PasswordField from '@/components/PasswordField.vue'
import router from '@/router'
import Swal from 'sweetalert2'

let token = null
let email = null
const isLoading = ref(false)
const password = ref('')
const confirmationPassword = ref('')
const errorMessage = ref(null)

async function requestPasswordChange() {
  if (password.value !== confirmationPassword.value) {
    return
  }

  isLoading.value = true

  try {
    const { response, data } = await customFetch(`/reset-password/${token}`, 'PUT', {
      token: token,
      email: email,
      password: password.value,
      password_confirmation: confirmationPassword.value
    })
    if (response.ok) {
      errorMessage.value = null
      Swal.fire({
        title: 'Success',
        text: data.message,
        icon: 'success'
      })
    } else {
      Swal.fire({
        title: `Error ${response.status}`,
        text: data.message,
        icon: 'error'
      })
      errorMessage.value = data.message ?? 'An error occurred. Please try again'
    }
  } catch (error) {
    console.error(error)
    Swal.fire({
      title: `Error`,
      text: error,
      icon: 'error'
    })
    errorMessage.value = { message: error.message }
  }

  isLoading.value = false
}

onMounted(() => {
  const params = new URLSearchParams(location.search)
  const resetPasswordToken = params.get('token')
  const userEmail = params.get('email')
  if (!resetPasswordToken || !userEmail) {
    Swal.fire({
      title: `Insufficient data for password reset`,
      toast: true,
      timer: 2000,
      position: 'top-end',
      showConfirmButton: false,
      icon: 'error'
    })
    router.push({ name: 'home' })
  }

  token = resetPasswordToken
  email = userEmail
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
          <password-field v-model="password" />
        </div>

        <div class="m-form-group">
          <password-field label="Confirm Password" v-model="confirmationPassword" />

          <!-- <label for="confirmPasswordInput" class="form-label">Confirm Password</label>
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
          </div> -->
        </div>

        <span v-show="password !== confirmationPassword" class="error">
          Passwords do not match.
        </span>

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

.error {
  color: red;
  font-size: 0.8em;
  padding-bottom: 1em;
}
</style>
