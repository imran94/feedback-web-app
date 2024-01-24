export default {
  getHeaders() {
    let headers = {
      'Content-Type': 'application/json'
    }
    if (localStorage.getItem('accessToken')) {
      headers['Authorization'] = localStorage.getItem('accessToken')
    }

    return headers
  }
}
