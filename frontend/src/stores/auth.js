import { ref, setTransitionHooks } from 'vue'
import { defineStore } from 'pinia'

// export const useAuthStore = defineStore('auth', () => {
//   const isAuth = ref(localStorage.getItem('accessToken') !== null)
//   const name = ref('')
//   const isAdmin = ref(false)
//   const userId = ref('')

//   function clearAll() {
//     isAuth.value = false
//     isAdmin.value = false
//     name.value = ''
//     userId.value = ''
//     localStorage.removeItem('accessToken')
//   }

//   function getAccessToken() {
//     return localStorage.getItem('accessToken')
//   }

//   function isAuthenticated() {
//     return localStorage.getItem('accessToken') !== null
//   }

//   return { 
//     isAuth, 
//     name, 
//     isAdmin, 
//     userId, 
//     clearAll, 
//     getAccessToken,
//     isAuthenticated,
//     login
//    }
// })

const ACCESS_TOKEN = 'accessToken'
const REFRESH_TOKEN = 'refreshToken'

export const useAuthStore = defineStore({
  id: 'auth',
  state: () => ({
    accessToken: localStorage.getItem(ACCESS_TOKEN),
    refreshToken: localStorage.getItem(REFRESH_TOKEN),
    isAdmin: false,
    name: '',
    userId: ''
  }),
  getters: {
    isAuthenticated: (state) => {return state.accessToken && state.refreshToken},
    getAccessToken: (state) => state.accessToken,
    getRefreshToken: (state) => state.refreshToken,
  },
  actions: {
    setUser(data) {
      this.updateTokens(data)

      this.isAdmin = data['is_admin'] ?? false 
      this.name = data.name
      this.userId = data.userId
    },

    updateTokens(data) {
      this.accessToken = data.accessToken
      this.refreshToken = data.refreshToken
      localStorage.setItem(ACCESS_TOKEN, data.accessToken)
      localStorage.setItem(REFRESH_TOKEN, data.refreshToken)
    },

    logout() {
      this.accessToken = null
      this.refreshToken = null
      localStorage.removeItem(ACCESS_TOKEN)
      localStorage.removeItem(REFRESH_TOKEN)

      this.isAdmin = false
      this.name = null
      this.userId = null
    }
  }
})
