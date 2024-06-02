<script setup>
import { ref, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import axios from 'axios'

const publicEnvVar = ref(import.meta.env.VITE_SOME_KEY)
const privateEnvVar = ref(import.meta.env.VITE_DB_PASSWORD)
const route = useRoute()

onMounted(() => {
  console.log(`${import.meta.env.VITE_BACKEND_URL}/test`)
  test()
  // console.log(process.env.VITE_SOME_KEY)
  // console.log(process.env.DB_PASSWORD)
})

async function test() {
  try {
    const { data } = await axios.get(`${import.meta.env.VITE_BACKEND_URL}/test`)
    console.log(data)
  } catch (error) {
    console.error(error)
  }
}
</script>

<template>
  <div class="section">
    <div class="loading">
      <div class="spinner-border" />
      <span>Verifying your email address...</span>
      <button @click="test()">Test</button>
    </div>
  </div>
</template>

<style scoped>
.loading {
  position: fixed;
  inset: 0;
  margin: auto;
  width: fit-content;
  height: fit-content;

  display: flex;
  align-items: center;
  justify-content: center;
  flex-direction: column;
  row-gap: 1rem;
}
</style>
