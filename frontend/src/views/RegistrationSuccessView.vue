<script setup>
import { useAuthStore } from '../stores/auth'

const auth = useAuthStore()
</script>

<template>
  <div class="body">
    <h1>
      Thank you for signing up<span v-if="auth.name">, {{ auth.name }}</span
      >!
    </h1>
    <div>A confirmation email has been sent to your email address.</div>
  </div>
</template>

<style scoped>
.body {
  display: flex;
  align-items: center;
  justify-content: center;
  flex-flow: column wrap;

  margin: 5em;
}
</style>
