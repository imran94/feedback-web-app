<script setup>
import { useThemeStore } from '@/stores/theme'
import { useAuthStore } from '@/stores/auth'
import { ref, onMounted, computed } from 'vue'
import router from './router'
import Swal from 'sweetalert2'
import { useRoute } from 'vue-router'

const theme = useThemeStore()
const auth = useAuthStore()
const route = useRoute()
const searchTerm = ref('')
const showMenu = ref(false)

function search() {
  router.push({
    name: 'search',
    query: { q: searchTerm.value }
  })
  searchTerm.value = ''
}

async function logout() {
  const res = await Swal.fire({
    title: 'Log Out',
    text: 'Are you sure you want to log out?',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#3085d6',
    cancelButtonColor: '#d33',
    confirmButtonText: 'Yes'
  })
  if (!res.isConfirmed) {
    return
  }

  auth.logout()
  Swal.fire({
    title: 'You have successfully logged out',
    toast: true,
    timer: 2000,
    position: 'bottom-end',
    showConfirmButton: false,
    icon: 'success'
  })
}

const isOnSearchPage = computed(() => router.currentRoute.value.name !== 'search')
</script>

<template>
  <header class="header" id="header">
    <nav class="nav container">
      <div class="nav-toggle" id="nav-toggle" @click="showMenu = true">
        <i class="bi bi-list"></i>
      </div>
      <RouterLink
        to="/"
        class="nav-link"
        :class="{ active: router.currentRoute.value.name === 'home' }"
      >
        Logo
      </RouterLink>

      <div class="nav-menu" :class="{ 'show-menu': showMenu }">
        <div class="nav-close" @click="showMenu = false">
          <i class="bi bi-x"></i>
        </div>
        <ul class="nav-list">
          <li class="nav-item" v-if="auth.isAuth">
            <router-link
              :to="{ name: 'createFeedbackForm' }"
              type="button"
              class="btn btn-primary add-button"
              @click="showMenu = false"
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
              @click="showMenu = false"
            >
              <i class="bi bi-house"></i>
              <span>Home</span>
            </RouterLink>
          </li>

          <li class="nav-item" v-show="auth.isAuth">
            <RouterLink
              to="/profile"
              class="nav-link"
              :class="{ active: route.name === 'profile' && route.params.id === '' }"
              @click="showMenu = false"
            >
              <i class="bi bi-person"></i>
              <span>Profile</span>
            </RouterLink>
          </li>

          <li class="nav-item" v-show="!auth.isAuth && !auth.isLoadingUser">
            <RouterLink
              to="/login"
              class="nav-link"
              :class="{ active: router.currentRoute.value.name === 'login' }"
              @click="showMenu = false"
            >
              <i class="bi bi-box-arrow-in-right"></i>
              <span>Login</span>
            </RouterLink>
          </li>

          <li class="nav-item" v-show="!auth.isAuth && !auth.isLoadingUser">
            <RouterLink
              to="/register"
              class="nav-link"
              :class="{ active: router.currentRoute.value.name === 'register' }"
              @click="showMenu = false"
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

          <li class="nav-item clickable theme-toggle-button" @click="theme.toggle">
            <i v-show="!theme.isDarkMode" class="bi bi-sun"></i>
            <i v-show="theme.isDarkMode" class="bi bi-moon"></i>
          </li>
        </ul>
      </div>
    </nav>
  </header>
  <RouterView />
</template>

<style scoped>
.nav-toggle {
  padding: 0rem 1rem;
}

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
  flex-direction: row;
  flex-wrap: nowrap;
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

.theme-toggle-button {
  /* color: light-dark(var(--light-bg), var(--light-bg));
  background: light-dark(var(--dark-bg), var(--light-bg)); */

  box-shadow: 1px 1px 3px 1px rgba(0, 0, 0, 0.4);

  border-radius: 50%;
  padding: 0.25em 0.5em;
}

@media screen and (max-width: 1150px) {
  nav {
    justify-content: flex-start;
  }

  .nav-menu {
    position: fixed;
    left: -100%;
    top: 0;
    background-color: var(--black-color);
    width: 100%;
    height: 100%;
    padding: 0.5rem 1rem;
    flex-direction: column;
    justify-content: space-between;
    transition: left 0.4s;
  }

  .nav-list {
    flex-direction: column;
  }

  .nav-item {
    transform: translateX(-150px);
    visibility: hidden;
    transition:
      transform 0.4s ease-out,
      visibility 0.4s;
  }

  .nav-item:nth-child(1) {
    transition-delay: 0.1s;
  }

  .nav-item:nth-child(2) {
    transition-delay: 0.2s;
  }

  .nav-item:nth-child(3) {
    transition-delay: 0.3s;
  }

  .nav-item:nth-child(4) {
    transition-delay: 0.4s;
  }

  .nav-item:nth-child(5) {
    transition-delay: 0.5s;
  }

  .nav-item:nth-child(6) {
    transition-delay: 0.6s;
  }

  .nav-item:nth-child(7) {
    transition-delay: 0.7s;
  }

  .nav-item:nth-child(8) {
    transition-delay: 0.8s;
  }

  .nav-link i {
    visibility: visible;
  }

  .nav-close,
  .nav-toggle {
    display: flex;
    color: var(--white-color);
    font-size: 1.5rem;
    cursor: pointer;
  }

  .show-menu {
    left: 0;
  }

  .show-menu .nav-item {
    visibility: visible;
    transform: translateX(0);
  }
}
</style>
