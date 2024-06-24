<script setup>
import { customFetch } from '@/utils'
import { ref, onMounted, watch } from 'vue'
import { useAuthStore } from '@/stores/auth'
import FeedbackList from '@/components/FeedbackList.vue'
import router from '@/router'
import { useRoute } from 'vue-router'

const route = useRoute()
const authStore = useAuthStore()
let userId = route.params.id !== '' ? route.params.id : authStore.userId ?? ''

const isLoading = ref(true)
const isError = ref(false)

const feedbackData = ref({
  data: [],
  current_page: 1,
  rows: 0,
  total: 0,
  per_page: 15
})

authStore.$subscribe((_, state) => {
  console.log('state change')
  // user looking at own profile but not authenticated and not loading info
  if (userId === '' && state.isAuth) {
    userId = state.userId
    fetchPosts()
  }

  if (route.params.id === '' && !state.isAuth && !state.isLoadingUser) {
    router.push({ name: 'home' })
  }
})

watch(
  () => route.params.id,
  (newId, oldId) => {
    console.log('route changed to ' + newId)
    userId = newId
    fetchPosts()
  }
)

onMounted(() => {
  if (userId !== '') {
    console.log('fetching for user ' + userId)
    fetchPosts()
  }
})

async function fetchPosts() {
  isLoading.value = true
  const { response, data } = await customFetch(
    `/user/${userId}/feedback?page=${feedbackData.value.current_page}&limit=${feedbackData.value.per_page}`
  )
  isLoading.value = false
  if (!response.ok) {
    isError.value = true
  } else {
    isError.value = false
    feedbackData.value = data
    if (screen.width <= 600) {
      // Shorten the number of page links to fit screen
      const links = feedbackData.value.links
      const newLinks = []

      let currentPage = feedbackData.value.current_page
      const currentPageIndex = links.findIndex((link) => parseInt(link.label) === currentPage)
      const prevOffset = currentPageIndex === links.length - 2 ? 4 : 2
      for (let i = currentPageIndex - 1; i > 1; i--) {
        newLinks.unshift(links[i])
        if (newLinks.length >= prevOffset) break
      }

      newLinks.push(links[currentPageIndex])

      for (let i = currentPageIndex + 1; i < links.length - 1; i++) {
        newLinks.push(links[i])
        if (newLinks.length >= 5) break
      }

      newLinks.unshift({
        url: links[0].url,
        label: '&laquo;',
        active: links[0].active
      })

      newLinks.push({
        url: links[links.length - 1].url,
        label: '&raquo;',
        active: links[links.length - 1].active
      })

      feedbackData.value.links = newLinks
    }
  }
}

function navigateToPage(link) {
  if (link.active) return

  if (link.label.includes('&laquo;')) {
    feedbackData.value.current_page--
  } else if (link.label.includes('&raquo;')) {
    feedbackData.value.current_page++
  } else {
    feedbackData.value.current_page = link.label
  }

  fetchPosts()
}
</script>

<template>
  <div class="body">
    <ul class="nav nav-pills nav-fill">
      <li class="nav-item">
        <span class="nav-link active" href="#">Info</span>
      </li>
      <li class="nav-item">
        <span class="nav-link" href="#">History</span>
      </li>
    </ul>

    <div id="userInfo" class="tab-content"></div>

    <div id="feedbackHistory" class="tab-content">
      <div v-show="isLoading && feedbackData.data.length === 0" class="spinner-border center" />

      <div v-show="feedbackData.data.length === 0 && !isLoading && !isError">
        Looks like you haven't added any feedback.
      </div>

      <div v-show="feedbackData.data.length === 0 && !isLoading && !isError">
        Looks like you haven't added any feedback.
      </div>

      <feedback-list :feedbackData="feedbackData" @page-no-clicked="navigateToPage($event)" />
    </div>
  </div>
</template>

<style scoped>
a {
  text-decoration: none;
}

.body,
.tab-content {
  width: 100%;

  display: flex;
  flex-flow: column wrap;
  justify-content: space-around;
  align-items: center;
}

.tab-content {
  padding: 0.5em 1em;
}

.m-card {
  margin: 1em 0.5em;
  padding: 1em;
  box-shadow: 2px 2px 5px 2px rgba(0, 0, 0, 0.4);
  border-radius: 0.5em;

  color: black;
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: flex-start;
}

a.m-card:hover {
  background-color: #ebebeb;
  box-shadow: 5px 5px 10px 5px rgba(0, 0, 0, 0.8);
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
