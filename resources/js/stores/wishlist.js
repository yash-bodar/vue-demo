import { defineStore } from 'pinia'
import { ref, computed } from 'vue'

export const useWishlistStore = defineStore('wishlist', () => {
  const items = ref([])
  const loading = ref(false)

  const wishlistCount = computed(() => items.value.length)

  async function fetchWishlist() {
    loading.value = true
    try {
      const axios = (await import('axios')).default
      const response = await axios.get('/api/fetch-wishlist')
      items.value = response.data.data || []
    } catch (error) {
      console.error('Failed to fetch wishlist:', error)
    } finally {
      loading.value = false
    }
  }

  async function addToWishlist(productId) {
    loading.value = true
    try {
      const axios = (await import('axios')).default
      // YB - 31-07-2026: Use update-wishlist route with action 'add'
      await axios.post('/api/update-wishlist', { product_id: productId, action: 'add' })
      await fetchWishlist()
      return { success: true }
    } catch (error) {
      return { success: false, error: error.response?.data?.message || 'Failed to add to wishlist' }
    } finally {
      loading.value = false
    }
  }

  async function removeFromWishlist(wishlistItemId) {
    loading.value = true
    try {
      // Find the item in local state to retrieve the product ID
      const item = items.value.find((i) => i.id === wishlistItemId)
      const productId = item ? item.product_id : wishlistItemId

      const axios = (await import('axios')).default
      // YB - 31-07-2026: Use update-wishlist route with action 'remove'
      await axios.post('/api/update-wishlist', { product_id: productId, action: 'remove' })
      await fetchWishlist()
      return { success: true }
    } catch {
      return { success: false, error: 'Failed to remove from wishlist' }
    } finally {
      loading.value = false
    }
  }

  function isInWishlist(productId) {
    return items.value.some((item) => item.product_id === productId)
  }

  function clearWishlist() {
    items.value = []
  }

  return {
    items,
    loading,
    wishlistCount,
    fetchWishlist,
    addToWishlist,
    removeFromWishlist,
    isInWishlist,
    clearWishlist,
  }
})
