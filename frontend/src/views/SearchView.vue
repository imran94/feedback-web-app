<script setup>
import { onMounted, ref } from 'vue';
import utils from '@/utils';


const searchTerm = ref('')

async function search() {

    const res = await utils.networkRequest(`/search?q=${searchTerm.value}`)
    if (res.status === 200) {
        const data = await res.json()
        console.log(data)
    }
}

onMounted(() => {
    searchTerm.value = new URLSearchParams(location.search).get('q')
    search()
})
</script>

<template>
    <div class="body">
        <form @submit.prevent="search">
            <div class="m-form-group">
                <label for="titleInput" class="form-label">Search</label>
                <input v-model="searchTerm" type="search" class="form-control search-field" maxlength="250" required>
            </div>
            <button class="btn btn-outline-success" type="submit">Search</button>
        </form>
    </div>
</template>

<style scoped>
.body {
    display: flex;

    flex-direction: row;
    align-items: center;
    justify-content: center;

    margin-top: 3em;
}

.body * {
    margin: 0.5em;
}

.search-field {
    width: 50em;
}
</style>