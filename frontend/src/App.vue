<script setup>
import { RouterLink, RouterView } from 'vue-router'
import { useAuthStore } from './stores/auth';
import { ref, onMounted, computed } from 'vue';
import router from './router';
import utils from './utils';

const auth = useAuthStore()
const searchTerm = ref('')

async function getUser() {
  const res = await utils.networkRequest('/user')
  if (res.status === 200) {
    const data = await res.json()
    auth.name = data.name
    auth.isAdmin = data['is_admin']
    auth.userId = data.id
  }
}

function search() {
  router.push({ name: 'search', query: { q: searchTerm.value } })
  searchTerm.value = ''
}

function logout() {
  auth.clearAll()
}

const isOnSearchPage = computed(() => router.currentRoute.value.name !== 'search')

onMounted(() => {
  if (auth.isAuth) {
    getUser()
  }
});

</script>

<template>
  <nav class="navbar navbar-expand-lg bg-body-tertiary">
    <div class="container-fluid">
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent"
        aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav me-auto mb-2 mb-lg-0">

          <li class="nav-item" v-show="auth.isAuth && auth.name">
            <span class="nav-link">Welcome, {{ auth.name }}</span>
          </li>
          <li class="nav-item">
            <RouterLink to="/" class="nav-link" :class="{ active: router.currentRoute.value.name === 'home' }">
              Home
            </RouterLink>
          </li>

          <li class="nav-item" v-show="auth.isAuth">
            <RouterLink to="/profile" class="nav-link" :class="{ active: router.currentRoute.value.name === 'profile' }">
              Profile
            </RouterLink>
          </li>
          <div class="nav-item" v-show="!auth.isAuth">
            <RouterLink to="/login" class="nav-link" :class="{ active: router.currentRoute.value.name === 'login' }">Login
            </RouterLink>
          </div>
          <div class="nav-item" v-show="!auth.isAuth">
            <RouterLink to="/register" class="nav-link"
              :class="{ active: router.currentRoute.value.name === 'register' }">Sign Up</RouterLink>
          </div>
        </ul>

        <router-link :to="{ name: 'createFeedbackForm' }" v-if="auth.isAuth" type="button"
          class="btn btn-primary add-button d-flex right-nav-item"><i class="bi bi-plus"></i> Add Feedback</router-link>

        <form v-if="router.currentRoute.value.name !== 'search'" @submit.prevent="search"
          class="d-flex search-bar right-nav-item" role="search">
          <input class="form-control me-2" v-model="searchTerm" type="search" placeholder="Search" required>
          <button class="btn btn-outline-success" type="submit">Search</button>
        </form>

        <div class="right-nav-item" v-show="auth.isAuth">
          <a href="javascript:void(0)" class="nav-link" @click="logout">Log Out</a>
        </div>
        <!-- <form class="d-flex" role="search">
          <input class="form-control me-2" type="search" placeholder="Search" aria-label="Search">
          <button class="btn btn-outline-success" type="submit">Search</button>
        </form> -->
      </div>
    </div>
  </nav>

  <RouterView />
</template>

<style scoped>
.search-bar {
  margin-right: 1em;
}

.right-nav-item {
  margin: 0.1em 0.2em;
}
</style>
