import './assets/main.css'
import 'bootstrap/dist/css/bootstrap.css'
import 'bootstrap/dist/js/bootstrap'
import 'bootstrap-icons/font/bootstrap-icons.css'
import PrimeVue from 'primevue/config'
import { createApp } from 'vue'
import { createPinia } from 'pinia'

import App from './App.vue'
import router from './router'

const app = createApp(App)

// app.config.globalProperties.$axios = axios
app.use(createPinia())
app.use(router)
app.use(PrimeVue)

app.mount('#app')
