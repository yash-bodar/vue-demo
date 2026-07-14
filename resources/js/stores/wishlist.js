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
      const response = await axios.get('/api/wishlist')
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
      const response = await axios.post('/api/wishlist/add', { product_id: productId })
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
      const axios = (await import('axios')).default
      await axios.delete(`/api/wishlist/${wishlistItemId}`)
      items.value = items.value.filter((item) => item.id !== wishlistItemId)
      return { success: true }
    } catch (error) {
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
