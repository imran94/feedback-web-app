export default {
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
    // Default options are marked with *

    const accessToken =
      localStorage.getItem('accessToken') !== null ? localStorage.getItem('accessToken') : null
    const fullUrl = `http://localhost:${import.meta.env.VITE_BACKEND_PORT}/api${url}`
    const response = await fetch(fullUrl, {
      method: method,
      mode: 'cors',
      cache: 'no-cache',
      credentials: 'same-origin',
      headers: {
        'Content-Type': 'application/json',
        Accept: 'application/json',
        Authorization: `Bearer ${accessToken}`
        // 'Content-Type': 'application/x-www-form-urlencoded',
      },
      redirect: 'follow', // manual, *follow, error
      referrerPolicy: 'no-referrer', // no-referrer, *no-referrer-when-downgrade, origin, origin-when-cross-origin, same-origin, strict-origin, strict-origin-when-cross-origin, unsafe-url
      body: method !== 'GET' ? JSON.stringify(data) : null // body data type must match "Content-Type" header
    })
    return response // parses JSON response into native JavaScript objects
  }
}
