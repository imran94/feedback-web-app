<script setup>
import { onMounted, ref, watch } from 'vue'
import utils from '@/utils'
import FeedbackList from '@/components/FeedbackList.vue'

const searchTerm = ref('')
const feedbackData = ref({
  data: [],
  current_page: 1,
  rows: 0,
  total: 0
})
const searchCategories = ref([])

function resetPageNo() {
  feedbackData.value.current_page = 1
}

watch(searchTerm, async () => {
  resetPageNo()
})

async function search() {
  console.log(
    'categories',
    searchCategories.value.filter((c) => c.isSelected).map((c) => c.name)
  )

  const res = await utils.networkRequest(
    `/search?page=${feedbackData.value.current_page}`,
    'POST',
    {
      search: searchTerm.value,
      categories: searchCategories.value.filter((c) => c.isSelected).map((c) => c.name)
    }
  )
  if (res.ok) {
    feedbackData.value = await res.json()
    if (screen.width > 600) {
      return
    }
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

function navigateToPage(link) {
  if (link.active) return

  if (link.label.includes('&laquo;')) {
    feedbackData.value.current_page--
  } else if (link.label.includes('&raquo;')) {
    feedbackData.value.current_page++
  } else {
    feedbackData.value.current_page = link.label
  }

  search()
}

function onCategorySelected() {
  console.log('onCategorySelected')
  resetPageNo()
  search()
}

onMounted(() => {
  searchTerm.value = new URLSearchParams(location.search).get('q')
  search()

  searchCategories.value = utils.getCategories().map((category) => {
    return {
      name: category,
      isSelected: false
    }
  })
})
</script>

<template>
  <h2 class="title">Search</h2>
  <div class="body">
    <form @submit.prevent="search" class="search-form">
      <label for="searchInput" class="form-label">Search</label>
      <input
        id="searchInput"
        v-model="searchTerm"
        type="search"
        class="form-control search-field"
        maxlength="250"
        required
      />
      <h6 for="filterInput" class="form-label">Filter By Categories:</h6>
      <div class="search-filter-group">
        <div class="category" v-for="category in searchCategories" :key="category.name">
          <input
            class="form-check-input"
            type="checkbox"
            :id="category.name"
            v-model="category.isSelected"
            @change="onCategorySelected"
          />
          <label class="form-check-label" :for="category.name">{{ category.name }}</label>
        </div>
      </div>
      <button class="btn btn-outline-success" type="submit">Search</button>
    </form>

    <feedback-list :feedbackData="feedbackData" @page-no-clicked="navigateToPage($event)" />
  </div>
</template>

<style scoped>
.title {
  padding: 0.5em;
}

.body {
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
}

.body * {
  margin: 10px;
}

.search-form {
  display: flex;
  flex-direction: column;
  justify-content: flex-start;
  align-items: flex-start;

  width: 600px;
  max-width: 600px;
}

.search-filter-group {
  border-radius: 5px;
  border: 1px solid lightgray;
  width: 100%;

  display: flex;
  flex-flow: row wrap;
  justify-content: flex-start;
  align-items: baseline;
}

.category {
  margin: 0;
  display: inline-flex;
  align-items: center;
}

.form-check-input {
  margin-right: 0.1em;
}

.form-check-label {
  margin-left: 0.2em;
}

@media (max-width: 1200px) {
  .search-form {
    width: 90%;
    max-width: 100%;
  }
}
</style>
