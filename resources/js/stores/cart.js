import { defineStore } from 'pinia'
import { ref, computed } from 'vue'

export const useCartStore = defineStore('cart', () => {
  const items = ref([])
  const loading = ref(false)

  const cartCount = computed(() => items.value.length)
  const cartTotal = computed(() => {
    return items.value.reduce((total, item) => {
      return total + item.price * item.quantity
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
      let pId = productId
      let qty = quantity
      let vId = null
      
      // YB - 31-07-2026: Support object arguments passed from components like Home.vue
      if (productId && typeof productId === 'object') {
        pId = productId.product_id || productId.id
        qty = productId.quantity || 1
        vId = productId.variant_id || productId.product_variant_id || null
      }
      
      const axios = (await import('axios')).default
      
      // Make sequential requests if quantity is greater than 1 (since /api/update-cart handles 1 increment per call)
      let lastResponse = null
      for (let i = 0; i < qty; i++) {
        lastResponse = await axios.post('/api/update-cart', {
          product_id: pId,
          variant_id: vId,
          action: 'increase',
        })
      }
      
      await fetchCart()
      return { success: lastResponse ? lastResponse.data.success : true }
    } catch (error) {
      return { success: false, error: error.response?.data?.message || 'Failed to add to cart' }
    } finally {
      loading.value = false
    }
  }

  async function removeFromCart(cartItemId) {
    loading.value = true
    try {
      const item = items.value.find((i) => i.id === cartItemId)
      if (!item) return { success: false, error: 'Item not found in cart' }
      
      const axios = (await import('axios')).default
      // Loop to decrement until item is removed
      let qty = item.quantity
      for (let i = 0; i < qty; i++) {
        await axios.post('/api/update-cart', {
          product_id: item.product_id,
          variant_id: item.product_variant_id,
          action: 'decrease',
        })
      }
      await fetchCart()
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
      const item = items.value.find((i) => i.id === cartItemId)
      if (!item) return { success: false, error: 'Item not found in cart' }
      
      const diff = quantity - item.quantity
      if (diff === 0) return { success: true }
      
      const axios = (await import('axios')).default
      const action = diff > 0 ? 'increase' : 'decrease'
      const steps = Math.abs(diff)
      for (let i = 0; i < steps; i++) {
        await axios.post('/api/update-cart', {
          product_id: item.product_id,
          variant_id: item.product_variant_id,
          action,
        })
      }
      await fetchCart()
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
