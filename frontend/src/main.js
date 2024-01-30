import './assets/main.css'
import 'bootstrap/dist/css/bootstrap.css'
import 'bootstrap/dist/js/bootstrap'
import 'bootstrap-icons/font/bootstrap-icons.css'
import PrimeVue from 'primevue/config'
import { createApp } from 'vue'
import { createPinia } from 'pinia'
import * as PusherPushNotifications from '@pusher/push-notifications-web'

import App from './App.vue'
import router from './router'

const app = createApp(App)

const beamsClient = new PusherPushNotifications.Client({
  instanceId: 'f0fc087a-1695-4e40-8114-f78d417c68e2'
})

beamsClient
  .start()
  .then((beamsClient) => beamsClient.getDeviceId())
  .then((deviceId) => console.log('Successfully registered with Beams. Device ID:', deviceId))
  .catch(console.error)

// app.config.globalProperties.$axios = axios
app.use(createPinia())
app.use(router)
app.use(PrimeVue)

app.mount('#app')
