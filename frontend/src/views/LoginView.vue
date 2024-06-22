<script setup>
import { ref } from 'vue'
import { customFetch } from '../utils'
import { useAuthStore } from '@/stores/auth'
import router from '@/router'

const auth = useAuthStore()
const isLoading = ref(false)
const email = ref('')
const password = ref('')
const isInputError = ref(false)
const errorMessage = ref({})
const rememberMe = ref(false)

async function tryLogin() {
  isLoading.value = true
  try {
    const { response, data } = await customFetch('/authenticate', 'POST', {
      email: email.value,
      password: password.value
    })
    if (response.ok) {
      auth.shouldRemember = rememberMe.value
      auth.setUser(data)

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
    <div class="content">
      <div class="login-social">
        <a href="#" class="btn btn-social btn-facebook">
          <i class="bi bi-facebook"></i>
          <span>Sign In with Facebook</span>
        </a>
        <a href="#" class="btn btn-social btn-twitter">
          <i class="bi bi-twitter"></i>
          <span>Sign In with Twitter</span>
        </a>
        <a href="#" class="btn btn-social btn-google">
          <i class="bi bi-google"></i>
          <span>Sign In with Google</span>
        </a>
        <a href="#" class="btn btn-social btn-linkedin">
          <i class="bi bi-linkedin"></i>
          <span>Sign In with LinkedIn</span>
        </a>
      </div>

      <div class="divider">
        <span>OR</span>
      </div>

      <form class="login-standard" @submit.prevent="tryLogin">
        <h4 class="title">Login</h4>

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
        </div>

        <div class="m-form-group options">
          <div class="form-check">
            <input
              class="form-check-input"
              type="checkbox"
              id="checkRememberMe"
              v-model="rememberMe"
            />
            <label class="form-check-label" for="checkRememberMe">Remember Me</label>
          </div>

          <router-link :to="{ name: 'forgotPassword' }">Forgot Password?</router-link>
        </div>

        <div v-show="errorMessage?.message" class="input-error">{{ errorMessage.message }}</div>
        <button type="submit" class="btn" :disabled="isLoading">
          <span v-show="!isLoading">Login</span>
          <div v-show="isLoading" class="spinner-grow" role="status"></div>
        </button>

        <div class="m-form-group" style="font-size: 0.8em">
          Don't have an account?
          <router-link :to="{ name: 'register' }">Register</router-link>
        </div>
      </form>
    </div>
  </div>
</template>

<style scoped>
.section {
  /* display: flex;
  align-items: center;
  justify-content: center; */

  position: fixed;
  inset: 0;
  width: fit-content;
  height: fit-content;
  margin: auto;
  padding: 1rem;

  margin-top: 3em;
}

.title {
  width: 100%;
  text-align: center;
}

.content {
  width: 900px;
  border: 1px solid silver;
  border-radius: 3%;

  display: flex;
  flex-direction: row;
  align-items: center;
  justify-content: space-around;
}

.btn {
  box-shadow: 2px 2px 5px 2px rgba(0, 0, 0, 0.4);
  background-color: royalblue;
  color: white;
  width: 100%;
  padding: 0.75em;
}

.btn:hover,
.btn:focus {
  filter: saturate(0.5);
}

.btn-social {
  display: inline-flex;
  align-items: center;
  justify-content: flex-start;
  padding: 0px;
}

.btn > i {
  padding: 0.65em 0.85em;
  height: 100%;
  background-color: #0000001a;
}

.btn > span {
  width: 100%;
  text-align: start;
  padding-left: 5%;
}

.btn-facebook {
  background-color: #3b5897;
}
.btn-twitter {
  background-color: #53abee;
}
.btn-google {
  background-color: #de4e3b;
}
.btn-linkedin {
  background-color: #00a1db;
}

.login-social,
.login-standard {
  width: 100%;
  padding: 20px 30px;

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

.options {
  display: flex;
  flex-flow: row wrap;
  /* align-items: space-between; */
  justify-content: space-between;
}

a {
  text-decoration: none;
}

a:hover {
  text-decoration: underline;
}

@media screen and (max-width: 500px) {
  .section {
    position: static;
    padding-left: 0;
    padding-right: 0;
    width: 100%;
  }

  .content {
    width: 100%;
    flex-direction: column;
  }

  .login-social,
  .login-standard {
    padding: 0.5em;
    border: none;
  }
}
</style>
