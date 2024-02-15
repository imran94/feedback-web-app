<script setup>
import utils from '@/utils';
import { ref, onMounted } from 'vue'
import { useAuthStore } from '../stores/auth';
import FeedbackList from '@/components/FeedbackList.vue';


const auth = useAuthStore()
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
    if (screen.width <= 600) {
      // Shorten the number of page links to fit screen
      const links = feedbackData.value.links
      const newLinks = []

      let currentPage = feedbackData.value.current_page
      const currentPageIndex = links.findIndex(link => parseInt(link.label) === currentPage)
      const prevOffset = currentPageIndex === links.length - 2 ? 4 : 2
      for (let i = currentPageIndex - 1; i > 1; i--) {
        newLinks.unshift(links[i])
        if (newLinks.length >= prevOffset)
          break
      }

      newLinks.push(links[currentPageIndex])

      for (let i = currentPageIndex + 1; i < links.length - 1; i++) {
        newLinks.push(links[i])
        if (newLinks.length >= 5)
          break
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
});

</script>

<template>
  <div class="section">

    <div v-show="feedbackData.data.length === 0" class="spinner-border center"></div>

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

.center {
  margin: 10em;
}

.add-button {
  margin: 1em;
}
</style>