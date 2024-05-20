<script setup>
import { RouterLink, RouterView } from 'vue-router'
import { useAuthStore } from './stores/auth'
import { ref, onMounted, computed } from 'vue'
import router from './router'
import utils from './utils'

const auth = useAuthStore()
const searchTerm = ref('')
const showMenu = ref(false)

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
  router.push({
    name: 'search',
    query: { q: searchTerm.value }
  })
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
})
</script>

<template>
  <header class="header" id="header">
    <nav class="nav container">
      <RouterLink
        to="/"
        class="nav-link"
        :class="{ active: router.currentRoute.value.name === 'home' }"
      >
        Logo
      </RouterLink>

      <div class="nav-menu" :class="{ 'show-menu': showMenu }">
        <ul class="nav-list">
          <li class="nav-item" v-if="auth.isAuth">
            <router-link
              :to="{ name: 'createFeedbackForm' }"
              type="button"
              class="btn btn-primary add-button"
            >
              <i class="bi bi-plus"></i>
              <span>Add Feedback</span>
            </router-link>
          </li>

          <li class="nav-item">
            <RouterLink
              to="/"
              class="nav-link"
              :class="{ active: router.currentRoute.value.name === 'home' }"
            >
              <i class="bi bi-house"></i>
              <span>Home</span>
            </RouterLink>
          </li>

          <li class="nav-item" v-show="auth.isAuth">
            <RouterLink
              to="/profile"
              class="nav-link"
              :class="{ active: router.currentRoute.value.name === 'profile' }"
            >
              <i class="bi bi-person"></i>
              <span>Profile</span>
            </RouterLink>
          </li>

          <li class="nav-item" v-show="!auth.isAuth">
            <RouterLink
              to="/login"
              class="nav-link"
              :class="{ active: router.currentRoute.value.name === 'login' }"
            >
              <i class="bi bi-box-arrow-in-right"></i>
              <span>Login</span>
            </RouterLink>
          </li>

          <li class="nav-item" v-show="!auth.isAuth">
            <RouterLink
              to="/register"
              class="nav-link"
              :class="{ active: router.currentRoute.value.name === 'register' }"
            >
              <i class="bi bi-person-fill-add"></i>
              <span>Sign Up</span>
            </RouterLink>
          </li>

          <li class="nav-item" v-show="auth.isAuth">
            <a href="javascript:void(0)" class="nav-link" @click="logout">
              <i class="bi bi-box-arrow-left"></i>
              <span>Log Out</span>
            </a>
          </li>
        </ul>

        <div class="nav-close" @click="showMenu = false">
          <i class="bi bi-x"></i>
        </div>
      </div>

      <div class="nav-toggle" id="nav-toggle" @click="showMenu = true">
        <i class="bi bi-list"></i>
      </div>
    </nav>
  </header>

  <!-- <nav class="navbar navbar-expand-lg bg-body-tertiary">
    <div class="container-fluid">
      <button
        class="navbar-toggler"
        type="button"
        data-bs-toggle="collapse"
        data-bs-target="#navbarSupportedContent"
        aria-controls="navbarSupportedContent"
        aria-expanded="false"
        aria-label="Toggle navigation"
      >
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav me-auto mb-2 mb-lg-0">
          <li class="nav-item" v-show="auth.isAuth && auth.name">
            <span class="nav-link">Welcome, {{ auth.name }}</span>
          </li>
          <li class="nav-item">
            <RouterLink
              to="/"
              class="nav-link"
              :class="{ active: router.currentRoute.value.name === 'home' }"
            >
              Home
            </RouterLink>
          </li>

          <li class="nav-item" v-show="auth.isAuth">
            <RouterLink
              to="/profile"
              class="nav-link"
              :class="{ active: router.currentRoute.value.name === 'profile' }"
            >
              Profile
            </RouterLink>
          </li>
          <div class="nav-item" v-show="!auth.isAuth">
            <RouterLink
              to="/login"
              class="nav-link"
              :class="{ active: router.currentRoute.value.name === 'login' }"
              >Login
            </RouterLink>
          </div>
          <div class="nav-item" v-show="!auth.isAuth">
            <RouterLink
              to="/register"
              class="nav-link"
              :class="{ active: router.currentRoute.value.name === 'register' }"
              >Sign Up</RouterLink
            >
          </div>
        </ul>

        <router-link
          :to="{ name: 'createFeedbackForm' }"
          v-if="auth.isAuth"
          type="button"
          class="btn btn-primary add-button d-flex right-nav-item"
          ><i class="bi bi-plus"></i> Add Feedback</router-link
        >

        <form
          v-if="router.currentRoute.value.name !== 'search'"
          @submit.prevent="search"
          class="d-flex search-bar right-nav-item"
          role="search"
        >
          <input
            class="form-control me-2"
            v-model="searchTerm"
            type="search"
            placeholder="Search"
            required
          />
          <button class="btn btn-outline-success" type="submit">Search</button>
        </form>

        <div class="right-nav-item" v-show="auth.isAuth">
          <a href="javascript:void(0)" class="nav-link" @click="logout">Log Out</a>
        </div>
      </div>
    </div>
  </nav> -->

  <RouterView />
</template>

<style scoped>
.search-bar {
  margin-right: 1em;
}

.right-nav-item {
  margin: 0.1em 0.2em;
}

/* HEADER & NAV */
.header {
  position: fixed;
  width: 100%;
  height: var(--header-height);
  top: 0;
  left: 0;
  background-color: var(--black-color);
  z-index: var(--z-fixed);
}

nav {
  display: flex;
  justify-content: space-between;
  align-items: center;
  flex-wrap: nowrap;

  height: 100%;
}

.nav-logo {
  color: var(--white-color);
  font-weight: var(--font-medium);
}

.nav-list {
  list-style: none;

  display: flex;
  flex-direction: row;
}

.nav-link {
  text-decoration: none;

  position: relative;
  color: var(--off-white-color);
  font-size: var(--h1-font-size);
  font-weight: var(--font-medium);
  display: inline-flex;
  align-items: center;
  transition: opacity 0.4s;
}

.nav-menu {
  height: 100%;
  display: flex;
  flex-flow: row nowrap;
}

.nav-list {
  flex-flow: row nowrap;
  align-items: center;
  justify-content: center;
  height: 100%;
}

.nav-link i {
  font-size: 1.5rem;
  position: absolute;
  opacity: 0;
  visibility: hidden;
  transition:
    opacity 0.4s,
    visibility 0.4s;
}

.nav-link span {
  position: relative;
  transition: margin 0.4s;
}

.nav-link span::after {
  content: '';
  position: absolute;
  left: 0;
  bottom: -6px;
  width: 0;
  height: 2px;
  background-color: var(--white-color);
  transition: width 0.4s ease-out;
}

.nav-link:hover {
  color: var(--white-color);
}

/* Animate link on hover */
.nav-link:hover span {
  margin-left: 2rem;
}

.nav-link:hover i {
  opacity: 1;
  visibility: visible;
}

.nav-link:hover span::after {
  width: 100%;
}

.nav-close,
.nav-toggle {
  display: none;
}

.active {
  color: var(--white-color);
}
</style>
