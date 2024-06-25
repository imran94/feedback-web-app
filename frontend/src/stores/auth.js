import { defineStore } from 'pinia'
import { ref } from 'vue'
import { customFetch, Enums } from '@/utils'

export const useAuthStore = defineStore('auth', () => {
  const isAdmin = ref(false)
  const username = ref(null)
  const userId = ref(null)
  const isAuth = ref(false)
  const isLoadingUser = ref(false)
  const accessToken = ref(null)
  const refreshToken = ref(null)
  const shouldRemember = ref(false)
  const areValuesInit = ref(false)

  const initValues = () => {
    if (areValuesInit.value) return

    accessToken.value =
      localStorage.getItem(Enums.ACCESS_TOKEN) ?? sessionStorage.getItem(Enums.ACCESS_TOKEN)
    refreshToken.value =
      localStorage.getItem(Enums.REFRESH_TOKEN) ?? sessionStorage.getItem(Enums.REFRESH_TOKEN)

    if (accessToken.value) {
      shouldRemember.value = true
    }

    areValuesInit.value = true
  }

  const init = async () => {
    initValues()

    if (accessToken.value && !isAuth.value) {
      isLoadingUser.value = true
      const { response, data } = await customFetch('/user')
      if (response.ok) {
        setUser(data)
      } else {
        logout()
        localStorage.clear()
      }
      isLoadingUser.value = false
    }
  }

  const updateTokens = (data) => {
    if (data.accessToken) {
      accessToken.value = data.accessToken
      if (shouldRemember.value) {
        localStorage.setItem(Enums.ACCESS_TOKEN, data.accessToken)
      } else {
        sessionStorage.setItem(Enums.ACCESS_TOKEN, data.accessToken)
      }
    }
    if (data.refreshToken) {
      refreshToken.value = data.refreshToken
      if (shouldRemember.value) {
        localStorage.setItem(Enums.REFRESH_TOKEN, data.refreshToken)
      } else {
        sessionStorage.setItem(Enums.REFRESH_TOKEN, data.refreshToken)
      }
    }
  }

  const setUser = async (data) => {
    console.log('set user')
    updateTokens(data)
    isAuth.value = true
    console.log('setUser isAuth', isAuth.value)
    isAdmin.value = data['is_admin'] ?? false
    console.log('setUser isAdmin', isAdmin.value)
    username.value = data.name
    console.log('setUser username', username.value)

    userId.value = data.id ?? data.userId
    console.log('setUser userId', userId.value)
  }

  const logout = () => {
    const requestBody = {
      accessToken:
        localStorage.getItem(Enums.ACCESS_TOKEN) ??
        sessionStorage.getItem(Enums.ACCESS_TOKEN) ??
        accessToken.value,
      refreshToken:
        localStorage.getItem(Enums.REFRESH_TOKEN) ??
        sessionStorage.getItem(Enums.REFRESH_TOKEN) ??
        refreshToken.value
    }
    localStorage.removeItem(Enums.ACCESS_TOKEN)
    sessionStorage.removeItem(Enums.ACCESS_TOKEN)
    localStorage.removeItem(Enums.REFRESH_TOKEN)
    sessionStorage.removeItem(Enums.REFRESH_TOKEN)
    accessToken.value = null
    refreshToken.value = null
    isAdmin.value = false
    userId.value = null
    username.value = null
    isAuth.value = false
    shouldRemember.value = false

    customFetch('/user/logout', 'DELETE', requestBody)
  }

  return {
    isAdmin,
    username,
    userId,
    isAuth,
    shouldRemember,
    accessToken,
    refreshToken,
    isLoadingUser,
    initValues,
    init,
    updateTokens,
    setUser,
    logout
  }
})
