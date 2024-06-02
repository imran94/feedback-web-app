import { useAuthStore } from '@/stores/auth'

let authStore = null
const baseUrl = import.meta.env.VITE_BACKEND_URL

export default {
  getCategories() {
    return [
      'Product Feedback',
      'Feature Requests',
      'Bug Reports',
      'Customer Reviews & In-app Ratings',
      'Complaints & Questions',
      'Praise & Appreciation Posts',
      'Customer Surveys',
      'Net Promoter Score (NPS) Surveys',
      'Customer Satisfaction Survey',
      'Customer Effort Score Feedback',
      'Sales Objections & Feedback',
      'Customer Churn Feedback'
    ]
  },

  getHeaders() {
    let headers = {
      'Content-Type': 'application/json'
    }
    if (localStorage.getItem('accessToken')) {
      headers['Authorization'] = localStorage.getItem('accessToken')
    }

    return headers
  },

  getPort() {
    return import.meta.env.VITE_BACKEND_PORT
  },

  async networkRequest(url = '', method = 'GET', data = {}) {
    if (!authStore) {
      authStore = useAuthStore()
    }
    console.log('auth.isAuth', authStore.isAuthenticated)

    const bearerToken =
      localStorage.getItem('accessToken') !== null 
      ? `Bearer ${localStorage.getItem('accessToken')}` 
      : null
    
    const response = await fetch(`${baseUrl}${url}`, {
      method: method,
      mode: 'cors',
      cache: 'no-cache',
      credentials: 'same-origin',
      headers: {
        'Content-Type': 'application/json',
        Accept: 'application/json',
        Authorization: bearerToken
      },
      redirect: 'follow',
      referrerPolicy: 'no-referrer',
      body: method !== 'GET' ? JSON.stringify(data) : null 
    })


    return response 
  },

  async customFetch(url = '', method = 'GET', request = {}) {
    if (!authStore) {
      authStore = useAuthStore()
    }

    const bearerToken =
      authStore.getAccessToken
      ? `Bearer ${authStore.getAccessToken}` 
      : null
    
    let response = await fetch(`${baseUrl}${url}`, {
      method: method,
      headers: {
        'Content-Type': 'application/json',
        Accept: 'application/json',
        Authorization: bearerToken
      },
      body: method !== 'GET' ? JSON.stringify(request) : null 
    })
    
    if (response.status === 401 && authStore.refreshToken) {
      console.log('Refreshing token')
      // try Refresh token
      const refreshResponse = await fetch(`${baseUrl}/user/refresh-token`, {
        headers: {
          'Content-Type': 'application/json',
          Accept: 'application/json',
          Authorization: `Bearer ${authStore.refreshToken}`
        },
      })

      console.log('Refresh response: ', refreshResponse.response)
      const refreshData = await refreshResponse.json()
      console.log('Refresh response data: ', refreshData)

      if (refreshResponse.status === 401) {
        authStore.logout()
      } else {
        authStore.updateTokens(refreshData)

        response = await fetch(`${baseUrl}${url}`, {
          method: method,
          headers: {
            'Content-Type': 'application/json',
            Accept: 'application/json',
            Authorization: bearerToken
          },
          body: method !== 'GET' ? JSON.stringify(request) : null 
        })
      }
    }
    const data = await response.json()
    return { response, data }
  },

  
}


