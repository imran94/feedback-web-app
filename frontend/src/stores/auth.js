import { defineStore } from 'pinia'
import { ref } from 'vue'
import { customFetch, Enums } from '@/utils'

export const useAuthStore = defineStore('auth', () => {
  const isAdmin = ref(false)
  const username = ref(null)
  const userId = ref(null)
  const isAuth = ref(false)
  const isLoading = ref(false)
  const accessToken = ref(null)
  const refreshToken = ref(null)

  const init = async () => {
    accessToken.value = localStorage.getItem(Enums.ACCESS_TOKEN)
    refreshToken.value = localStorage.getItem(Enums.REFRESH_TOKEN)

    if (accessToken.value && !isAuth.value) {
      isLoading.value = true
      const { response, data } = await customFetch('/user')
      if (response.ok) {
        setUser(data)
      } else {
        localStorage.clear()
      }
      isLoading.value = false
    }
  }

  const updateTokens = (data) => {
    if (data.accessToken) {
      localStorage.setItem(Enums.ACCESS_TOKEN, data.accessToken)
    }
    if (data.refreshToken) {
      localStorage.setItem(Enums.REFRESH_TOKEN, data.refreshToken)
    }
  }

  const setUser = (data, rememberMe = false) => {
    if (rememberMe) {
      updateTokens(data)
    }
    isAuth.value = true
    isAdmin.value = data['is_admin'] ?? false
    username.value = data.name
    userId.value = data.id
  }

  const logout = () => {
    const requestBody = {
      accessToken: localStorage.getItem(Enums.ACCESS_TOKEN),
      refreshToken: localStorage.getItem(Enums.REFRESH_TOKEN)
    }
    localStorage.removeItem(Enums.ACCESS_TOKEN)
    localStorage.removeItem(Enums.REFRESH_TOKEN)
    isAdmin.value = false
    userId.value = null
    username.value = null
    isAuth.value = false

    customFetch('/user/logout', 'DELETE', requestBody)
  }

  return {
    isAdmin,
    username,
    userId,
    isAuth,
    init,
    updateTokens,
    setUser,
    logout
  }
})
