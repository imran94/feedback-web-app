<script setup>
import { RouterLink, RouterView } from 'vue-router'
import { useAuthStore } from './stores/auth';
import { ref, onMounted } from 'vue';
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
}

function logout() {
  auth.clearAll()
}

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
            <RouterLink to="/profile" class="nav-link" :class="{ active: router.currentRoute.value.name == 'profile' }">
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

        <form @submit.prevent="search" class="d-flex search-bar" role="search">
          <input class="form-control me-2" v-model="searchTerm" type="search" placeholder="Search" required>
          <button class="btn btn-outline-success" type="submit">Search</button>
        </form>

        <div class="nav-item" v-show="auth.isAuth">
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
</style>
