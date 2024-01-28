<script setup>
import utils from '@/utils';
import { ref, onMounted } from 'vue'
import { useAuthStore } from '../stores/auth';

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
    const res = await utils.networkRequest(`/user/feedback?page=${feedbackData.value.current_page}&limit=${feedbackData.value.per_page}`)
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

            console.log('currentPageIndex', currentPageIndex)
            console.log('currentPage', links[currentPageIndex])
            console.log('newLinks', newLinks)

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
        <nav>
            <ul class="pagination">
                <template v-for="link in feedbackData.links" :key="link.label">
                    <li class="page-item" :class="{ disabled: !link.url }">
                        <a class="page-link" :class="{ active: link.active }" href="javascript:void(0)"
                            @click="navigateToPage(link)" v-html="link.label" />
                    </li>
                </template>
            </ul>
        </nav>

        <div v-show="isLoading" class="spinner-border center"></div>

        <router-link :to="{ name: 'createFeedbackForm' }" v-if="auth.isAuth" type="button" class="btn btn-primary">Add
            Feedback</router-link>

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

    display: flex;
    flex-flow: column wrap;
    justify-content: space-around;
    align-items: center;

    margin-top: 1em;
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