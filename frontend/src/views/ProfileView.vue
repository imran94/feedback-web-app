<script setup>
import { customFetch } from '@/utils'
import { ref, onMounted } from 'vue'
import { useAuthStore } from '@/stores/auth'
import FeedbackList from '@/components/FeedbackList.vue'
import router from '@/router'

const isLoading = ref(true)
const isError = ref(false)

const feedbackData = ref({
  data: [],
  current_page: 1,
  rows: 0,
  total: 0,
  per_page: 15
})

const authStore = useAuthStore()
authStore.$subscribe((_, state) => {
  if (!state.isAuth) {
    router.push({ name: 'home' })
  }
})

async function fetchPosts() {
  isLoading.value = true
  const { response, data } = await customFetch(
    `/user/feedback?page=${feedbackData.value.current_page}&limit=${feedbackData.value.per_page}`
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

onMounted(() => {
  fetchPosts()
})
</script>

<template>
  <div class="section">
    <div v-show="isLoading && feedbackData.data.length === 0" class="spinner-border center"></div>

    <div v-show="feedbackData.data.length === 0 && !isLoading && !isError">
      Looks like you haven't added any feedback.
    </div>

    <div v-show="feedbackData.data.length === 0 && !isLoading && !isError">
      Looks like you haven't added any feedback.
    </div>

    <feedback-list :feedbackData="feedbackData" @page-no-clicked="navigateToPage($event)" />
  </div>
</template>

<style scoped>
a {
  text-decoration: none;
}

.section {
  padding: 1em 0em;

  display: flex;
  flex-flow: column wrap;
  justify-content: space-around;
  align-items: center;
}

.m-card {
  width: 95%;
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
