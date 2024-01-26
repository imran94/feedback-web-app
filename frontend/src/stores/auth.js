import { ref } from 'vue'
import { defineStore } from 'pinia'

export const useAuthStore = defineStore('auth', () => {
  const isAuth = ref(localStorage.getItem('accessToken') != null)
  const name = ref('')
  const isAdmin = ref(false)
  const userId = ref('')

  function clearAll() {
    isAuth.value = false
    isAdmin.value = false
    name.value = ''
    userId.value = ''
    localStorage.removeItem('accessToken')
  }

  return { isAuth, name, isAdmin, userId, clearAll }
})
