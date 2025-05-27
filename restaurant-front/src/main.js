import { createApp } from 'vue'
import App from './App.vue'
import router from './router'
import Toast from 'vue-toastification'
import 'vue-toastification/dist/index.css'
import axios from 'axios'
import './assets/tailwind.css'

// ✅ Set base URL for API
axios.defaults.baseURL = 'http://localhost:8000/api'
axios.defaults.withCredentials = true

// ✅ Set token automatically in header if exists
const token = localStorage.getItem('token')
if (token) {
  axios.defaults.headers.common['Authorization'] = `Bearer ${token}`
}

// ✅ Add request interceptor to handle the token and add headers
axios.interceptors.request.use(
  (config) => {
    const token = localStorage.getItem('token')
    if (token) {
      config.headers['Authorization'] = `Bearer ${token}`
    }
    return config
  },
  (error) => {
    return Promise.reject(error)
  }
)

// ✅ Add response interceptor to handle error cases (e.g., unauthorized)
axios.interceptors.response.use(
  (response) => {
    return response
  },
  (error) => {
    // If the token is expired or invalid, clear the token and redirect to login
    if (error.response && error.response.status === 401) {
      localStorage.removeItem('token')
      router.push({ name: 'Login' }) // assuming 'Login' is the name of the login route
    }
    return Promise.reject(error)
  }
)

const app = createApp(App)

app.use(router)
app.use(Toast)

app.mount('#app')
