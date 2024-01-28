<script setup>
import utils from '@/utils';
import { ref, onMounted } from 'vue'
import { useAuthStore } from '../stores/auth';

const auth = useAuthStore()
const posts = ref([])
const isLoading = ref(true)
const feedbackData = ref({
  data: [],
  current_page: 1,
  rows: 0,
  total: 0,
  per_page: 15,
})

async function fetchPosts() {
  isLoading.value = true
  const res = await utils.networkRequest(`/feedback?page=${feedbackData.value.current_page}&limit=${feedbackData.value.per_page}`)
  isLoading.value = false
  if (res.ok) {
    feedbackData.value = await res.json()
    const links = feedbackData.value.links
    feedbackData.value.links = links.map(link => {
      if (link.label.includes('Previous')) {
        link.label = 'Previous'
      }

      if (link.label.includes('Next')) {
        link.label = 'Next'
      }

      return link
    })
  }
}

function navigateToPage(link) {
  if (link.active) return

  switch (link.label) {
    case 'Previous':
      feedbackData.value.current_page--
      break
    case 'Next':
      feedbackData.value.current_page++
      break
    default:
      feedbackData.value.current_page = link.label
      break
  }
  fetchPosts()
  // feedbackData.value.current_page = link.
}

onMounted(() => {
  fetchPosts()
});

</script>

<template>
  <div class="section">
    <nav>
      <ul class="pagination">
        <template v-for="link in feedbackData.links" :key="link.label">
          <li class="page-item" :class="{ disabled: !link.url }">
            <a class="page-link" :class="{ active: link.active }" href="javascript:void(0)" @click="navigateToPage(link)">
              {{ link.label }}
            </a>
          </li>
        </template>
      </ul>
    </nav>


    <router-link :to="{ name: 'createFeedbackForm' }" v-if="auth.isAuth" type="button" class="btn btn-primary">Add
      Feedback</router-link>

    <div v-show="isLoading" class="spinner-border center"></div>


    <router-link v-for="post in feedbackData.data" :to="{ name: 'feedbackThread', params: { id: post.id } }"
      :key="post.id" class="m-card">
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

  margin-top: 1em;

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