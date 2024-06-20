<script setup>
import { ref } from 'vue'
import { customFetch } from '../utils'
import { useAuthStore } from '@/stores/auth'
import router from '@/router'
import Swal from 'sweetalert2'

const isLoading = ref(false)
const email = ref('')
const errorMessage = ref(null)

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
</script>

<template>
  <div class="section">
    <div class="body">
      <div class="heading">
        <h4 class="title">Forgot Password</h4>
        <div class="caption">
          Enter the email address associated with your account and you'll receive a link to reset
          your password
        </div>
        <div class="caption">
          Remembered your password? <RouterLink to="/login">Login here</RouterLink>
        </div>
      </div>

      <form @submit.prevent="requestPasswordChange">
        <div class="m-form-group">
          <label for="emailInput" class="form-label">Email address</label>
          <input
            type="email"
            id="emailInput"
            v-model="email"
            class="form-control"
            placeholder="name@example.com"
            maxlength="255"
            required
          />
          <div v-if="errorMessage">
            <span class="error">
              {{ errorMessage }}
            </span>
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
</style>
