<script setup>
import { onMounted, ref, watch } from 'vue';
import utils from '@/utils';
import FeedbackList from '@/components/FeedbackList.vue';


const searchTerm = ref('')
const feedbackData = ref({
    data: [],
    current_page: 1,
    rows: 0,
    total: 0
})

watch(searchTerm, async () => {
    feedbackData.value.current_page = 1
})

async function search() {

    const res = await utils.networkRequest(`/search?q=${searchTerm.value}&page=${feedbackData.value.current_page}`)
    if (res.ok) {
        feedbackData.value = await res.json()
        if (screen.width > 600) {
            return
        }
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

onMounted(() => {
    searchTerm.value = new URLSearchParams(location.search).get('q')
    search()
})
</script>

<template>
    <div class="body">
        <form @submit.prevent="search" class="search-form">
            <div class="m-form-group">
                <label for="titleInput" class="form-label">Search</label>
                <input v-model="searchTerm" type="search" class="form-control search-field" maxlength="250" required>
            </div>
            <button class="btn btn-outline-success" type="submit">Search</button>
        </form>

        <feedback-list :feedbackData="feedbackData" @page-no-clicked="navigateToPage($event)" />
    </div>
</template>

<style scoped>
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
    justify-content: center;
    align-items: center;
}

.search-field {
    width: 600px;
}

@media (max-width: 1200px) {

    .search-form,
    .search-field {
        width: 100%;
    }
}
</style>