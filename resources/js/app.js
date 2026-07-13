import './bootstrap'
import '.././css/custom.css'
import { createApp } from 'vue'
import { createPinia } from 'pinia'

import App from './components/App.vue'
import router from './router'
import axios from 'axios'

// Set base URL to match your backend
axios.defaults.baseURL = import.meta.env.VITE_APP_URL
axios.defaults.withCredentials = true
const token = document.querySelector('meta[name="csrf-token"]')
if (token) {
  axios.defaults.headers.common['X-CSRF-TOKEN'] = token.getAttribute('content')
} else {
  console.error('CSRF token not found')
}
axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest'

const app = createApp(App)
const pinia = createPinia()

app.use(pinia)
app.use(router)

// Keep global properties for existing components
app.config.globalProperties.$axios = axios

app.mount('#app')

