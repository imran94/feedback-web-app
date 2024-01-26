<script setup>
import utils from '@/utils';
import { ref, onMounted } from 'vue'
import { useAuthStore } from '../stores/auth';

const auth = useAuthStore()
const posts = ref([])
const isLoading = ref(true)

async function fetchPosts() {
  isLoading.value = true
  const res = await utils.networkRequest('/feedback')
  isLoading.value = false
  if (res.status === 200) {
    posts.value = await res.json()
  }
}

onMounted(() => {
  fetchPosts()
});

</script>

<template>
  <div class="section">
    <div v-show="isLoading" class="spinner-border center"></div>

    <router-link :to="{ name: 'createFeedbackForm' }" v-if="auth.isAuth" type="button" class="btn btn-primary">Add
      Feedback</router-link>

    <router-link v-for="post in posts" :to="{ name: 'feedbackThread', params: { id: post.id } }" :key="post.id"
      class="m-card">
      <h4 class="m-card-title">{{ post.title }}</h4>

      <div class="m-card-subtitle">
        <i class="bi bi-tag-fill"></i>
        <span>{{ post.category }}</span>
      </div>

      <div class="m-card-subtitle">
        <i class="bi bi-person-fill"></i>
        <span>{{ post.user.name }}</span>
      </div>

      <div class="m-card-subtitle">
        <i class="bi bi-hand-thumbs-up-fill"></i>
        <span>{{ post['vote_count'] }}</span>
      </div>
    </router-link>
  </div>
</template>

<style scoped>
a {
  text-decoration: none;
}

.section {
  width: 100%;
  height: 100%;

  display: flex;
  flex-flow: column wrap;
  justify-content: space-around;
  align-items: center;
}

.m-card {
  width: 95%;
  margin: 1em 0.5em;
  padding: 1em;
  box-shadow: 2px 2px 5px 2px rgba(0, 0, 0, .4);
  border-radius: 0.5em;

  color: black;
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: flex-start;
}

a.m-card:hover {
  background-color: #ebebeb;
  box-shadow: 5px 5px 10px 5px rgba(0, 0, 0, .8);
}

.m-card-subtitle {
  display: inline-flex;
  justify-content: space-between;
}

.m-card-subtitle * {
  padding-right: 5px;
}

.center {
  margin: 10em;
}
</style>