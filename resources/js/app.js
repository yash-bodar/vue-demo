import './bootstrap'
import '.././css/custom.css'
import { createApp } from 'vue'
import { createPinia } from 'pinia'
import $ from 'jquery'
import select2 from 'select2'

select2($)

import 'select2/dist/css/select2.min.css'
import 'select2-bootstrap-5-theme/dist/select2-bootstrap-5-theme.min.css'

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

// Global Select2 directive
app.directive('select2', {
  mounted(el, binding) {
    // Initialize Select2 with common configuration
    const options = {
      theme: 'bootstrap-5',
      width: binding.value?.width || '100%',
      placeholder: binding.value?.placeholder || 'Select an option',
      allowClear: binding.value?.allowClear !== false,
      dropdownParent: binding.value?.dropdownParent || null,
      selectionCssClass: 'form-select',
      ...binding.value
    }

    $(el).select2(options)

    // Sync v-model with Select2
    $(el).on('select2:select', (e) => {
      el.dispatchEvent(new Event('change', { bubbles: true }))
    })

    $(el).on('select2:unselect', (e) => {
      el.dispatchEvent(new Event('change', { bubbles: true }))
    })
  },
  updated(el) {
    // Reinitialize Select2 when options change
    $(el).trigger('change')
  },
  unmounted(el) {
    // Destroy Select2 instance
    $(el).select2('destroy')
  }
})

app.mount('#app')

