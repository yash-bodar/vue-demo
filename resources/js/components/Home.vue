<template>
  <div class="container-xl py-4">
    <!-- Hero Banner Section -->
    <div class="hero-banner mb-5 text-white position-relative overflow-hidden shadow-vuexy-lg">
      <div class="row align-items-center position-relative" style="z-index: 2;">
        <div class="col-lg-7 px-lg-4">
          <span class="badge bg-label-primary text-white px-3 py-2 rounded-pill fw-semibold mb-3">
            <i class="fas fa-bolt me-2 text-warning"></i>Summer Sale 2026 Up To 50% OFF
          </span>
          <h1 class="display-4 fw-bold mb-3 text-heading text-white" style="letter-spacing: -0.02em;">
            Discover Luxury & Modern Shopping
          </h1>
          <p class="fs-5 opacity-90 mb-4" style="max-width: 580px;">
            Explore handpicked fashion collections, electronics, and accessories. Enjoy instant checkout with Stripe and express global shipping.
          </p>
          <div class="d-flex flex-wrap gap-3">
            <router-link to="/product" class="btn btn-primary btn-lg px-4 py-3 rounded-pill fw-bold shadow-primary">
              <i class="fas fa-bag-shopping me-2"></i>Shop Collection
            </router-link>
            <router-link to="/product" class="btn btn-outline-light btn-lg px-4 py-3 rounded-pill fw-bold">
              <i class="fas fa-tags me-2"></i>View Categories
            </router-link>
          </div>
        </div>

        <div class="col-lg-5 text-center d-none d-lg-block">
          <div class="p-4 bg-white bg-opacity-10 rounded-4 border border-white border-opacity-20 backdrop-blur" style="animation: float 6s ease-in-out infinite;">
            <i class="fas fa-store fa-8x text-white mb-3"></i>
            <div class="fw-bold fs-5 text-white">Verified Authentic Store</div>
            <span class="fs-8 text-white-50">Over 10,000+ satisfied customers worldwide</span>
          </div>
        </div>
      </div>
    </div>

    <!-- Category Shortcuts Grid -->
    <div class="mb-5" v-if="categories.length > 0">
      <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
          <h3 class="fw-bold m-0">Top Categories</h3>
          <small class="text-muted">Browse items by category</small>
        </div>
        <router-link to="/product" class="btn btn-sm btn-label-primary rounded-pill px-3">
          Explore All <i class="fas fa-arrow-right ms-1 fs-8"></i>
        </router-link>
      </div>

      <div class="row g-3">
        <div v-for="cat in categories.slice(0, 6)" :key="cat.id" class="col-lg-2 col-md-4 col-6">
          <router-link :to="{ path: '/product', query: { category: cat.id } }" class="card card-vuexy p-3 text-center text-decoration-none h-100 cursor-pointer">
            <div class="badge bg-label-primary rounded-circle p-3 mx-auto mb-2" style="width: 56px; height: 56px;">
              <i class="fas fa-layer-group fs-4"></i>
            </div>
            <div class="fw-bold fs-7 text-heading text-truncate">{{ cat.name }}</div>
          </router-link>
        </div>
      </div>
    </div>

    <!-- Value Propositions Row -->
    <div class="row g-4 mb-5">
      <div class="col-md-6 col-lg-3">
        <div class="card card-vuexy p-4 text-center h-100">
          <div class="badge bg-label-primary rounded-circle p-3 mx-auto mb-3" style="width: 56px; height: 56px;">
            <i class="fas fa-truck-fast fs-4"></i>
          </div>
          <h5 class="fw-bold mb-2">Free Express Delivery</h5>
          <p class="text-muted fs-7 mb-0">Free priority shipping on all qualified orders over $99 USD.</p>
        </div>
      </div>

      <div class="col-md-6 col-lg-3">
        <div class="card card-vuexy p-4 text-center h-100">
          <div class="badge bg-label-success rounded-circle p-3 mx-auto mb-3" style="width: 56px; height: 56px;">
            <i class="fas fa-shield-check fs-4"></i>
          </div>
          <h5 class="fw-bold mb-2">Secure Stripe Checkout</h5>
          <p class="text-muted fs-7 mb-0">256-bit SSL encrypted payments processed instantly.</p>
        </div>
      </div>

      <div class="col-md-6 col-lg-3">
        <div class="card card-vuexy p-4 text-center h-100">
          <div class="badge bg-label-warning rounded-circle p-3 mx-auto mb-3" style="width: 56px; height: 56px;">
            <i class="fas fa-rotate-left fs-4"></i>
          </div>
          <h5 class="fw-bold mb-2">30-Day Money Back</h5>
          <p class="text-muted fs-7 mb-0">Hassle-free 30 days return policy on eligible catalog goods.</p>
        </div>
      </div>

      <div class="col-md-6 col-lg-3">
        <div class="card card-vuexy p-4 text-center h-100">
          <div class="badge bg-label-info rounded-circle p-3 mx-auto mb-3" style="width: 56px; height: 56px;">
            <i class="fas fa-headset fs-4"></i>
          </div>
          <h5 class="fw-bold mb-2">24/7 Live Support</h5>
          <p class="text-muted fs-7 mb-0">Dedicated customer care assistance around the clock.</p>
        </div>
      </div>
    </div>

    <!-- Featured Products Section -->
    <div class="mb-5">
      <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
          <h3 class="fw-bold m-0">Featured Products</h3>
          <small class="text-muted">Handpicked trending items</small>
        </div>
        <router-link to="/product" class="btn btn-sm btn-label-primary rounded-pill px-3">
          View Store Catalog <i class="fas fa-arrow-right ms-1 fs-8"></i>
        </router-link>
      </div>

      <div v-if="loading" class="row g-4">
        <div v-for="n in 4" :key="n" class="col-lg-3 col-md-6">
          <div class="card card-vuexy p-3">
            <div class="skeleton-box mb-3" style="height: 200px;"></div>
            <div class="skeleton-box py-2 mb-2 w-75"></div>
            <div class="skeleton-box py-2 w-50"></div>
          </div>
        </div>
      </div>

      <div v-else class="row g-4">
        <div v-for="product in featuredProducts" :key="product.id" class="col-lg-3 col-md-6">
          <div class="card card-vuexy h-100">
            <div class="product-card-img-wrapper">
              <img :src="getProductImage(product)" :alt="product.name" class="product-card-img" />
              <div class="product-card-overlay">
                <router-link :to="`/product/detail/${product.id}`" class="btn btn-sm btn-light rounded-circle btn-icon shadow">
                  <i class="fas fa-eye text-primary"></i>
                </router-link>
                <button class="btn btn-sm btn-light rounded-circle btn-icon shadow" @click="toggleWishlist(product)">
                  <i class="fas fa-heart text-danger"></i>
                </button>
              </div>
              <span v-if="product.discount" class="position-absolute top-0 start-0 m-2 badge bg-danger rounded-pill fs-9">
                -{{ product.discount }}%
              </span>
            </div>

            <div class="p-3 d-flex flex-column flex-grow-1">
              <small class="text-muted fs-8 mb-1 text-uppercase">{{ product.category_name || 'Store Item' }}</small>
              <router-link :to="`/product/detail/${product.id}`" class="text-heading fw-bold fs-6 text-decoration-none mb-2 text-truncate">
                {{ product.name }}
              </router-link>
              
              <div class="mt-auto d-flex align-items-center justify-content-between pt-2 border-top">
                <div class="fw-bold text-primary fs-5">{{ formatCurrency(product.price) }}</div>
                <button class="btn btn-sm btn-primary rounded-pill px-3" @click="addToCart(product)">
                  <i class="fas fa-cart-plus me-1"></i> Add
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { useCartStore } from '../stores/cart'
import { useWishlistStore } from '../stores/wishlist'

export default {
  name: 'Home',
  data() {
    return {
      categories: [],
      featuredProducts: [],
      loading: false
    }
  },
  async mounted() {
    await this.fetchHomeData()
  },
  methods: {
    async fetchHomeData() {
      this.loading = true
      try {
        const [catRes, prodRes] = await Promise.all([
          this.$axios.get('/api/categories'),
          this.$axios.get('/api/products')
        ])
        this.categories = catRes.data.categories || catRes.data.data || catRes.data || []
        const prods = prodRes.data.products || prodRes.data.data || prodRes.data || []
        this.featuredProducts = prods.slice(0, 8)
      } catch (err) {
        console.error('Home data load error', err)
      } finally {
        this.loading = false
      }
    },
    getProductImage(prod) {
      if (prod.image) return prod.image.startsWith('http') ? prod.image : `/storage/${prod.image}`
      return 'https://via.placeholder.com/400x400?text=Product'
    },
    formatCurrency(amount) {
      return `$${parseFloat(amount || 0).toFixed(2)}`
    },
    async addToCart(prod) {
      const cartStore = useCartStore()
      if (cartStore.addToCart) {
        await cartStore.addToCart({ product_id: prod.id, quantity: 1 })
      }
    },
    async toggleWishlist(prod) {
      const wishlistStore = useWishlistStore()
      if (wishlistStore.toggleWishlist) {
        await wishlistStore.toggleWishlist(prod.id)
      }
    }
  }
}
</script>

<style scoped>
@keyframes float {
  0% { transform: translateY(0px); }
  50% { transform: translateY(-8px); }
  100% { transform: translateY(0px); }
}
</style>
