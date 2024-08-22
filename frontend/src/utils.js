import { useAuthStore } from '@/stores/auth'
import Cookies from 'js-cookie'

let authStore = null
const baseUrl = import.meta.env.VITE_BACKEND_BASE_URL

const Enums = Object.freeze({
  ACCESS_TOKEN: 'accessToken',
  REFRESH_TOKEN: 'refreshToken'
})
const CONTENT_TYPE = Object.freeze({
  JSON: 'application/json',
  FORM_DATA: null
})

const categories = ['UI', 'UX', 'Enhancement', 'Bug', 'Feature']

const customFetch = async (url = '', method = 'GET', body = {}, isJson = true) => {
  if (!authStore) {
    authStore = useAuthStore()
  }
  authStore.initValues()

  if (!url.startsWith('http')) {
    url = `${baseUrl}/api${url}`
  }
  let { response, data } = await networkRequest(url, method, body, authStore.accessToken, isJson)

  if (response.status !== 401 || (response.status === 401 && !authStore.isAuth)) {
    return { response, data }
  }

  if (!authStore.refreshToken) {
    authStore.logout()
    return { response, data }
  }

  const refreshResponse = await networkRequest(
    `${baseUrl}/api/user/refresh-token`,
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
  if (!Cookies.get('XSRF-TOKEN')) {
    await fetch(`${baseUrl}/sanctum/csrf-cookie`, {
      credentials: 'include'
    })
  }

  const headers = {
    Accept: 'application/json',
    Authorization: token ? `Bearer ${token}` : null,
    'X-XSRF-TOKEN': encodeURIComponent(Cookies.get('XSRF-TOKEN'))
  }
  if (isJson) {
    headers['Content-Type'] = CONTENT_TYPE.JSON
  }

  const response = await fetch(url, {
    // credentials: 'include',
    method: method,
    headers: headers,
    body: method !== 'GET' ? (isJson ? JSON.stringify(body) : body) : null
  })
  const data = await response.json()
  return { response, data }
}

export { Enums, categories, customFetch }
