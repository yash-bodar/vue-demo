import { defineStore } from 'pinia'
import { ref, computed } from 'vue'

export const useCartStore = defineStore('cart', () => {
  const items = ref([])
  const loading = ref(false)

  const cartCount = computed(() => items.value.length)
  const cartTotal = computed(() => {
    return items.value.reduce((total, item) => {
      return total + (item.price * item.quantity)
    }, 0)
  })

  async function fetchCart() {
    loading.value = true
    try {
      const axios = (await import('axios')).default
      const response = await axios.get('/api/cart')
      items.value = response.data.data || []
    } catch (error) {
      console.error('Failed to fetch cart:', error)
    } finally {
      loading.value = false
    }
  }

  async function addToCart(productId, quantity = 1) {
    loading.value = true
    try {
      const axios = (await import('axios')).default
      const response = await axios.post('/api/cart/add', {
        product_id: productId,
        quantity,
      })
      await fetchCart()
      return { success: true }
    } catch (error) {
      return { success: false, error: error.response?.data?.message || 'Failed to add to cart' }
    } finally {
      loading.value = false
    }
  }

  async function removeFromCart(cartItemId) {
    loading.value = true
    try {
      const axios = (await import('axios')).default
      await axios.delete(`/api/cart/${cartItemId}`)
      items.value = items.value.filter(item => item.id !== cartItemId)
      return { success: true }
    } catch (error) {
      return { success: false, error: 'Failed to remove from cart' }
    } finally {
      loading.value = false
    }
  }

  async function updateQuantity(cartItemId, quantity) {
    loading.value = true
    try {
      const axios = (await import('axios')).default
      await axios.put(`/api/cart/${cartItemId}`, { quantity })
      const item = items.value.find(item => item.id === cartItemId)
      if (item) {
        item.quantity = quantity
      }
      return { success: true }
    } catch (error) {
      return { success: false, error: 'Failed to update quantity' }
    } finally {
      loading.value = false
    }
  }

  function clearCart() {
    items.value = []
  }

  return {
    items,
    loading,
    cartCount,
    cartTotal,
    fetchCart,
    addToCart,
    removeFromCart,
    updateQuantity,
    clearCart,
  }
})
