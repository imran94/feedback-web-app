import { useAuthStore } from '@/stores/auth'

let authStore = null
const baseUrl = import.meta.env.VITE_BACKEND_URL

const Enums = Object.freeze({
  ACCESS_TOKEN: 'accessToken',
  REFRESH_TOKEN: 'refreshToken'
})
const CONTENT_TYPE = Object.freeze({
  JSON: 'application/json',
  FORM_DATA: null
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

const customFetch = async (url = '', method = 'GET', body = {}, isJson = true) => {
  if (!authStore) {
    authStore = useAuthStore()
  }
  authStore.initValues()

  let { response, data } = await networkRequest(url, method, body, authStore.accessToken, isJson)

  if (response.status !== 401 || (response.status === 401 && !authStore.isAuth)) {
    return { response, data }
  }

  if (!authStore.refreshToken) {
    authStore.logout()
    return { response, data }
  }

  const refreshResponse = await networkRequest(
    '/user/refresh-token',
    'GET',
    {},
    authStore.refreshToken
  )
  const refreshData = refreshResponse.data

  if (!refreshResponse.response.ok) {
    authStore.logout()
    return { response, data }
  }

  authStore.updateTokens(refreshData)

  let newResponse = await networkRequest(url, method, body, refreshData.accessToken, isJson)
  response = newResponse.response
  data = newResponse.data

  return newResponse
}

const networkRequest = async (url = '', method = 'GET', body = {}, token, isJson = true) => {
  const headers = {
    Accept: 'application/json',
    Authorization: token ? `Bearer ${token}` : null
  }
  if (isJson) {
    headers['Content-Type'] = CONTENT_TYPE.JSON
  }

  const response = await fetch(`${baseUrl}${url}`, {
    method: method,
    headers: headers,
    body: method !== 'GET' ? (isJson ? JSON.stringify(body) : body) : null
  })
  const data = await response.json()
  return { response, data }
}

export { Enums, categories, customFetch }
