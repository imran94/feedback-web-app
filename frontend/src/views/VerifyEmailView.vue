<script setup>
import { ref } from 'vue'
import { useRoute } from 'vue-router'
import axios from 'axios'

const route = useRoute()

const test = () => {}
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
