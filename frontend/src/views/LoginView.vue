<script setup>
import { ref } from 'vue'
import utils from '../utils'
import { useAuthStore } from '../stores/auth'
import router from '@/router'

const auth = useAuthStore()
const isLoading = ref(false)
const email = ref('')
const password = ref('')
const isInputError = ref(false)
const errorMessage = ref({})

async function tryLogin() {
  isLoading.value = true
  try {
    const { response, data } = await utils.customFetch('/authenticate', 'POST', {
      email: email.value,
      password: password.value
    })
    if (response.ok) {
      auth.setUser(data)
      // auth.setTokens(data.accessToken, data.refreshToken)
      // auth.isAuth = true
      // auth.name = data.name
      // auth.isAdmin = data.isAdmin
      // auth.userId = data.userId

      router.push({ name: 'home' })
    } else {
      isInputError.value = true
      if (response.status === 401) {
        errorMessage.value = data
      } else {
        errorMessage.value = { message: 'An error occurred.' }
      }
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
      <h4 class="title">Login</h4>

      <form @submit.prevent="tryLogin">
        <div class="m-form-group">
          <label for="emailInput" class="form-label">Email address</label>
          <input
            type="email"
            id="emailInput"
            v-model="email"
            class="form-control"
            placeholder="name@example.com"
            required
          />
        </div>

        <div class="m-form-group">
          <label for="passwordInput" class="form-label">Password</label>
          <input
            type="password"
            id="passwordInput"
            v-model="password"
            class="form-control"
            required
          />
          <span style="font-size: 0.8em"
            >Don't have an account?
            <router-link :to="{ name: 'register' }">Sign up</router-link></span
          >
        </div>

        <div v-show="errorMessage?.message" class="input-error">{{ errorMessage.message }}</div>
        <button type="submit" class="btn btn-primary" :disabled="isLoading">
          <span v-show="!isLoading">Login</span>
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

  row-gap: 1rem;
}

.m-form-group {
  width: 100%;
}

.input-error {
  color: red;
  font-size: 0.8em;
  padding-top: 0.2em;
  padding-left: 0.5em;
  padding-bottom: 0.5em;
}

@media screen and (max-width: 500px) {
  .body {
    width: 100%;
  }
}
</style>
