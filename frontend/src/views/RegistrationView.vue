<script setup>
import { ref } from 'vue'
import utils from '../utils'
import { useAuthStore } from '../stores/auth'
import router from '@/router'

const auth = useAuthStore()
const isLoading = ref(false)
const name = ref('')
const email = ref('')
const password = ref('')
const isAdmin = ref(false)
const isInputError = ref(false)
const errorMessage = ref('')

async function register() {
  try {
    isLoading.value = true
    const response = await utils.networkRequest('/register', 'POST', {
      name: name.value,
      email: email.value,
      password: password.value
    })
    const data = await response.json()
    if (response.status === 200) {
      localStorage.setItem('accessToken', data.accessToken)
      auth.isAuth = true
      auth.name = data.name
      auth.isAdmin = data.isAdmin
      auth.userId = data.userId

      router.push({ name: 'registrationSuccess' })
    } else {
      errorMessage.value = data
      isInputError.value = true
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
      <h4 class="title">Sign Up</h4>

      <form @submit.prevent="register">
        <div class="m-form-group">
          <label for="nameInput" class="form-label">Name</label>
          <input
            type="text"
            id="nameInput"
            v-model="name"
            class="form-control"
            maxlength="255"
            required
          />
          <div v-if="errorMessage?.errors?.name">
            <span v-for="errorText in errorMessage.errors.name" :key="errorText" class="error">
              {{ errorText }}
            </span>
          </div>
        </div>

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
          <div v-if="errorMessage?.errors?.email">
            <span v-for="errorText in errorMessage.errors.email" :key="errorText" class="error">
              {{ errorText }}
            </span>
          </div>
        </div>

        <div class="m-form-group">
          <label for="passwordInput" class="form-label">Password</label>
          <input
            type="password"
            id="passwordInput"
            v-model="password"
            class="form-control"
            minlength="8"
          />
          <div v-if="errorMessage?.errors?.password">
            <span v-for="errorText in errorMessage.errors.password" :key="errorText" class="error">
              {{ errorText }}
            </span>
          </div>
        </div>

        <div class="m-form-group form-check">
          <input class="form-check-input" type="checkbox" v-model="isAdmin" id="isAdminCheckbox" />
          <label class="form-check-label" for="isAdminCheckbox"> Admin </label>
        </div>

        <div class="m-form-group">
          <span style="font-size: 0.8em"
            >Already have an account?
            <router-link :to="{ name: 'login' }">Sign in</router-link></span
          >
        </div>
        <div v-show="errorMessage?.message" class="general-error">{{ errorMessage.message }}</div>
        <button type="submit" class="btn btn-primary" :disabled="isLoading">
          <span v-show="!isLoading">Register</span>
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

.title {
  width: 100%;
  text-align: center;
  margin-bottom: 1.5em;
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
