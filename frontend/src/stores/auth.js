import { defineStore } from 'pinia'
import { ref } from 'vue'
import { customFetch, Enums } from '@/utils'

export const useAuthStore = defineStore('auth', () => {
  const isAdmin = ref(false)
  const username = ref(null)
  const userId = ref(null)
  const isAuth = ref(false)

  const init = async () => {
    console.log('init')
    if (localStorage.getItem(Enums.ACCESS_TOKEN) && !isAuth.value) {
      const { response, data } = await customFetch('/user')
      console.log('get user response', response.status)
      console.log('get user response data', data)
      if (response.ok) {
        console.log('response ok. setting user')
        setUser(data)
      } else {
        console.log('clearing storage')
        localStorage.clear()
      }
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

  const setUser = (data) => {
    updateTokens(data)
    isAuth.value = true
    isAdmin.value = data['is_admin'] ?? false
    username.value = data.name
    userId.value = data.id
  }

  const logout = () => {
    localStorage.removeItem(Enums.ACCESS_TOKEN)
    localStorage.removeItem(Enums.REFRESH_TOKEN)
    isAdmin.value = false
    userId.value = null
    username.value = null
    isAuth.value = false
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
