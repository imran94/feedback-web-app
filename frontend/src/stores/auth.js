import { ref, computed } from 'vue'
import { defineStore } from 'pinia'

export const useAuthStore = defineStore('auth', () => {
  const isAuth = ref(localStorage.getItem('accessToken') != null)
  return { isAuth }
})
