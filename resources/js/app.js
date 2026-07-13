import './bootstrap';
import '.././css/custom.css';
import { createApp } from 'vue'

import App from './components/App.vue'
import router from './router'
import axios from 'axios'
import { getImageUrl } from './utils/ImageUrl';
import { formatDate } from './utils/formatDate';
import { showToast } from './utils/ui-toasts';
import { getOrderStatusBadgeClass } from './utils/statusBadge';
import { sortByField, getSortIcon } from './utils/table';
import { showSwalMessage,confirmAction } from './utils/showMessage';

// Set base URL to match your backend
axios.defaults.baseURL = import.meta.env.VITE_APP_URL;
axios.defaults.withCredentials = true;
const token = document.querySelector('meta[name="csrf-token"]');
if (token) {
    axios.defaults.headers.common['X-CSRF-TOKEN'] = token.getAttribute('content');
} else {
    console.error('CSRF token not found');
}
axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

const app = createApp(App)
app.use(router) 
app.config.globalProperties.$axios = axios
app.config.globalProperties.$getImageUrl = getImageUrl
app.config.globalProperties.$formatDate = formatDate
app.config.globalProperties.$toast = showToast;
app.config.globalProperties.$getOrderStatusBadgeClass = getOrderStatusBadgeClass;
app.config.globalProperties.$sortByField = sortByField;
app.config.globalProperties.$getSortIcon = getSortIcon;
app.config.globalProperties.$showSwalMessage = showSwalMessage;
app.config.globalProperties.$confirmAction = confirmAction;
app.mount('#app')

