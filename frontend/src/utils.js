import { useAuthStore } from '@/stores/auth'

let authStore = null
const baseUrl = import.meta.env.VITE_BACKEND_URL

const Enums = Object.freeze({
  ACCESS_TOKEN: 'accessToken',
  REFRESH_TOKEN: 'refreshToken'
})

const categories = [
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

const customFetch = async (url = '', method = 'GET', request = {}) => {
  if (!authStore) {
    authStore = useAuthStore()
  }

  const accessToken = localStorage.getItem(Enums.ACCESS_TOKEN)
  let { response, data } = await networkRequest(url, method, request, accessToken)

  if (response.status !== 401) {
    return { response, data }
  }

  console.log('first 401')
  const refreshToken = localStorage.getItem(Enums.REFRESH_TOKEN)
  console.log('refreshToken: ', refreshToken)

  if (!refreshToken) {
    return { response, data }
  }

  console.log('refreshing token')
  const refreshResponse = await networkRequest('/user/refresh-token', 'GET', {}, refreshToken)
  console.log('refresh response:', refreshResponse.response)
  const refreshData = refreshResponse.data
  console.log('refresh data:', refreshData)

  if (!refreshResponse.response.ok) {
    console.log('Failed to refresh token. Logging out')
    authStore.logout()
    return { response, data }
  }

  localStorage.setItem(Enums.ACCESS_TOKEN, refreshData.accessToken)
  localStorage.setItem(Enums.REFRESH_TOKEN, refreshData.refreshToken)

  console.log('Token successfully refreshed. Retrying original request')
  let newResponse = await networkRequest(url, method, request, refreshData.accessToken)
  console.log('newResponse response', newResponse)
  console.log('newResponse data', newResponse.data)
  response = newResponse.response
  data = newResponse.data

  return newResponse
}

const networkRequest = async (url = '', method = 'GET', request = {}, token) => {
  console.log('networkRequest token: ', `${url}: ${token}`)
  const response = await fetch(`${baseUrl}${url}`, {
    method: method,
    headers: {
      'Content-Type': 'application/json',
      Accept: 'application/json',
      Authorization: token ? `Bearer ${token}` : null
    },
    body: method !== 'GET' ? JSON.stringify(request) : null
  })
  const data = await response.json()
  return { response, data }
}

export { Enums, categories, customFetch }
