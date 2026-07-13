import { defineStore } from 'pinia'
import { ref, computed } from 'vue'

export const useAuthStore = defineStore('auth', () => {
  const user = ref(null)
  const token = ref(null)
  const loading = ref(false)

  const isAuthenticated = computed(() => !!user.value)
  const isAdmin = computed(() => user.value?.role === 'admin')

  async function fetchUser() {
    loading.value = true
    try {
      const axios = (await import('axios')).default
      const response = await axios.get('/user')
      user.value = response.data
    } catch (error) {
      console.error('Failed to fetch user:', error)
      user.value = null
    } finally {
      loading.value = false
    }
  }

  async function login(credentials) {
    loading.value = true
    try {
      const axios = (await import('axios')).default
      const response = await axios.post('/login', credentials)
      user.value = response.data.user
      token.value = response.data.token
      return { success: true }
    } catch (error) {
      return { success: false, error: error.response?.data?.message || 'Login failed' }
    } finally {
      loading.value = false
    }
  }

  async function logout() {
    loading.value = true
    try {
      const axios = (await import('axios')).default
      await axios.post('/logout')
      user.value = null
      token.value = null
    } catch (error) {
      console.error('Logout failed:', error)
    } finally {
      loading.value = false
    }
  }

  function setUser(userData) {
    user.value = userData
  }

  return {
    user,
    token,
    loading,
    isAuthenticated,
    isAdmin,
    fetchUser,
    login,
    logout,
    setUser,
  }
})
