<script setup>
import { RouterLink, RouterView } from 'vue-router'
import { useAuthStore } from './stores/auth';
import { ref, onMounted } from 'vue';
import router from './router';
import utils from './utils';

const auth = useAuthStore()
async function getUser() {
  const res = await utils.networkRequest('/user')
  if (res.status === 200) {
    const data = await res.json()
    auth.name = data.name
    auth.isAdmin = data['is_admin']
    auth.userId = data.id
  }
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
.topnav {
  background-color: #333;
  overflow: hidden;
}

/* Style the links inside the navigation bar */
.topnav a {
  float: left;
  display: block;
  color: #f2f2f2;
  text-align: center;
  padding: 14px 16px;
  text-decoration: none;
  font-size: 17px;
}

/* Change the color of links on hover */
.topnav a:hover {
  background-color: #ddd;
  color: black;
}

/* Add an active class to highlight the current page */
.topnav a.active {
  background-color: #04AA6D;
  color: white;
}

/* Hide the link that should open and close the topnav on small screens */
.topnav .icon {
  display: none;
}

/* When the screen is less than 600 pixels wide, hide all links, except for the first one ("Home"). Show the link that contains should open and close the topnav (.icon) */
@media screen and (max-width: 600px) {
  .topnav a:not(:first-child) {
    display: none;
  }

  .topnav a.icon {
    float: right;
    display: block;
  }
}

/* The "responsive" class is added to the topnav with JavaScript when the user clicks on the icon. This class makes the topnav look good on small screens (display the links vertically instead of horizontally) */
@media screen and (max-width: 600px) {
  .topnav.responsive {
    position: relative;
  }

  .topnav.responsive a.icon {
    position: absolute;
    right: 0;
    top: 0;
  }

  .topnav.responsive a {
    float: none;
    display: block;
    text-align: left;
  }
}
</style>
