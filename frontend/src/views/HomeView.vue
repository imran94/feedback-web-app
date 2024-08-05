<script setup>
import { customFetch, categories } from '@/utils'
import { ref, onMounted, watch } from 'vue'
import { useAuthStore } from '@/stores/auth'
import FeedbackList from '@/components/FeedbackList.vue'
import { useRoute } from 'vue-router'
import router from '@/router'

const route = useRoute()

const isLoading = ref(true)
const feedbackData = ref({
  data: [],
  current_page: 1,
  rows: 0,
  total: 0,
  per_page: 15
})

const baseUrl = import.meta.env.VITE_BACKEND_URL

watch(() => route.query.category, fetchPosts, { immediate: true })
onMounted(() => fetchPosts())

async function fetchPosts() {
  isLoading.value = true
  const { response, data } = await customFetch(
    `/feedback?page=${feedbackData.value.current_page}&limit=${feedbackData.value.per_page}&category=${route.query.category ?? ''}`
  )
  isLoading.value = false
  if (response.ok) {
    feedbackData.value = data
    if (screen.width <= 600) {
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

async function navigateToPage(link) {
  if (link.active) return

  if (link.label.includes('&laquo;')) {
    feedbackData.value.current_page--
  } else if (link.label.includes('&raquo;')) {
    feedbackData.value.current_page++
  } else {
    feedbackData.value.current_page = link.label
  }

  await fetchPosts()
  window.scrollTo({
    top: 0,
    // left: 0,
    behavior: 'smooth'
  })
}

function setCategory(selectedCategory) {
  router.push({ query: { category: selectedCategory } })
}
</script>

<template>
  <div class="body">
    <div class="sidebar">
      <div class="filter-selector">
        <div
          class="category clickable"
          :class="{
            'category-selected': !route.query.category
          }"
          @click="setCategory('')"
        >
          All
        </div>

        <div
          v-for="category in categories"
          :key="category"
          class="category clickable"
          :class="{ 'category-selected': route.query.category === category }"
          @click="setCategory(category)"
        >
          {{ category }}
        </div>
      </div>
    </div>

    <feedback-list
      :feedbackData="feedbackData"
      :isLoading="isLoading"
      emptyMessage="No feedback posts to show."
      @page-no-clicked="navigateToPage($event)"
    />
  </div>
</template>

<style scoped>
.body {
  margin-top: 1em;
  padding: 5em 1em 1em 1em;

  display: flex;
  flex-direction: row;
  align-items: flex-start;
  justify-content: center;
  row-gap: 5em;
}

.sidebar {
  margin-right: 2em;

  display: flex;
  flex-direction: column;
  align-items: flex-start;
  justify-content: space-between;
  row-gap: 1em;
}

.sidebar {
  width: 20%;
}

.filter-selector {
  width: 100%;
  padding: 1em 2em;
  border-radius: 0.5em;

  color: light-dark(var(--light-text), var(--dark-text));
  background: light-dark(var(--light-card-bg), var(--dark-card-bg));
  display: flex;
  flex-flow: row wrap;
  justify-content: flex-start;
  align-items: flex-start;
  row-gap: 0.5em;
}

.filter-selector .category {
  margin-right: 0.5em;
}

.category-selected {
  background-color: var(--card-fg-text-light);
  color: white;
}

.feedback-list,
.loading-bar {
  width: 60%;
}
.loading-bar {
  display: flex;
  flex-direction: column;
  align-items: center;
}

@media screen and (max-width: 1024px) {
  .body {
    flex-direction: column;
    row-gap: 1em;
  }

  .sidebar {
    width: 100%;
    flex-direction: row;
  }

  .feedback-list {
    width: 100%;
  }
}
</style>
