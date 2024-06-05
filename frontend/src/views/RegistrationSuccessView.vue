<script setup>
import { useAuthStore } from '../stores/auth'

const auth = useAuthStore()
</script>

<template>
  <div class="body">
    <h1>
      Thank you for signing up<span v-if="auth.username">, {{ auth.username }}</span
      >!
    </h1>
    <div>A confirmation email has been sent to your email address.</div>
    <RouterLink to="/" class="btn btn-primary">Return to Home</RouterLink>
  </div>
</template>

<style scoped>
.body {
  position: fixed;
  inset: 0;
  width: fit-content;
  height: fit-content;
  margin: auto;
  padding: 1rem;

  display: flex;
  align-items: center;
  justify-content: center;
  flex-flow: column wrap;
  row-gap: 0.5rem;
}
</style>
