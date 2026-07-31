<template>
  <div class="container-xl py-4">
    <!-- Header Section -->
    <div class="card card-vuexy p-4 mb-4">
      <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
        <div class="d-flex align-items-center gap-3">
          <div class="badge bg-label-danger rounded-3 p-3">
            <i class="fas fa-heart fs-3"></i>
          </div>
          <div>
            <h4 class="mb-0 fw-bold text-heading">My Wishlist</h4>
            <small class="text-muted">Saved favorite items ({{ productCount }})</small>
          </div>
        </div>
        <router-link to="/product" class="btn btn-outline-primary rounded-pill px-4">
          <i class="fas fa-store me-2"></i>Explore Store
        </router-link>
      </div>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="row g-4">
      <div v-for="n in 4" :key="n" class="col-sm-6 col-md-6 col-lg-4 col-xl-3">
        <div class="card card-vuexy p-3">
          <div class="skeleton-box mb-3 skeleton-h-200"></div>
          <div class="skeleton-box py-2 mb-2 w-75"></div>
          <div class="skeleton-box py-2 w-50"></div>
        </div>
      </div>
    </div>

    <!-- Empty Wishlist State -->
    <div v-else-if="products.length === 0" class="card card-vuexy p-5 text-center">
      <i class="far fa-heart fa-4x text-muted mb-3 opacity-50"></i>
      <h4 class="fw-bold text-heading">Your Wishlist is Empty</h4>
      <p class="text-muted fs-7 mb-4">
        Browse our collection and click the heart icon on any item to save it here.
      </p>
      <router-link to="/product" class="btn btn-primary rounded-pill px-4 py-2 mx-auto">
        <i class="fas fa-bag-shopping me-2"></i>Browse Products
      </router-link>
    </div>

    <!-- Wishlist Cards Grid -->
    <div v-else>
      <div class="row g-4 mb-4">
        <div
          v-for="(product, index) in products"
          :key="index"
          class="col-sm-6 col-md-6 col-lg-4 col-xl-3"
        >
          <div class="card card-vuexy h-100 d-flex flex-column">
            <!-- Image & Wishlist Float Remove -->
            <div class="product-card-img-wrapper">
              <img :src="getImageUrl(product.image)" class="product-card-img" :alt="product.name" />
              <button
                type="button"
                class="btn btn-sm btn-light rounded-circle btn-icon shadow position-absolute top-0 end-0 m-2"
                :disabled="loadingProductId == product.id"
                @click="updateWishlist(product.id, 'remove')"
                title="Remove from Wishlist"
              >
                <i class="fas fa-heart text-danger"></i>
              </button>
            </div>

            <div class="p-3 d-flex flex-column flex-grow-1">
              <div
                class="cursor-pointer mb-2"
                @click="$router.push(`/product/detail/${product.id}`)"
              >
                <h6 class="fw-bold text-heading mb-1 text-truncate">{{ product.name }}</h6>
                <p class="text-muted fs-8 mb-0 text-truncate">
                  {{ product.description || 'No description available' }}
                </p>
              </div>

              <div
                class="mt-auto pt-2 border-top d-flex align-items-center justify-content-between"
              >
                <span class="fw-bold text-primary fs-5"
                  >${{ parseFloat(product.price || 0).toFixed(2) }}</span
                >
                <button
                  class="btn btn-sm btn-primary rounded-pill px-3"
                  @click="moveToCart(product)"
                >
                  <i class="fas fa-cart-plus me-1"></i>Move to Cart
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Pagination -->
      <div class="d-flex justify-content-center" v-if="lastPage > 1">
        <div class="card card-vuexy p-3 px-4">
          <nav>
            <ul class="pagination mb-0 gap-2">
              <li class="page-item" :class="{ disabled: currentPage === 1 }">
                <button
                  class="page-link rounded-circle border-0 text-primary"
                  :disabled="currentPage === 1"
                  @click="loadWishlist(currentPage - 1)"
                >
                  <i class="fas fa-chevron-left"></i>
                </button>
              </li>
              <li
                v-for="page in lastPage"
                :key="page"
                class="page-item"
                :class="{ active: page === currentPage }"
              >
                <button
                  class="page-link rounded-circle border-0 fw-bold"
                  :class="page === currentPage ? 'bg-primary text-white' : 'text-primary'"
                  @click="loadWishlist(page)"
                >
                  {{ page }}
                </button>
              </li>
              <li class="page-item" :class="{ disabled: currentPage === lastPage }">
                <button
                  class="page-link rounded-circle border-0 text-primary"
                  :disabled="currentPage === lastPage"
                  @click="loadWishlist(currentPage + 1)"
                >
                  <i class="fas fa-chevron-right"></i>
                </button>
              </li>
            </ul>
          </nav>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { getImageUrl } from '../utils/ImageUrl'
import { useCartStore } from '../stores/cart'

export default {
  name: 'MyWishlist',
  data() {
    return {
      products: [],
      loadingProductId: null,
      loading: false,
      wishlist: [],
      productCount: 0,
      currentPage: 1,
      perPage: 8,
      lastPage: 1,
      filters: {},
    }
  },
  mounted() {
    this.loadWishlist()
  },
  methods: {
    getImageUrl,
    async loadWishlist(page = 1) {
      this.loading = true
      try {
        const params = new URLSearchParams({
          page: page,
          ...this.filters,
        })
        const response = await this.$axios.get(`/api/get-wishlist?${params.toString()}`)
        const data = response.data
        if (data.success) {
          this.wishlist = data.data.data || data.data || []
          this.productCount = data.data.total || this.wishlist.length
          this.currentPage = data.data.current_page || 1
          this.lastPage = data.data.last_page || 1
          this.perPage = data.data.per_page || 8
          this.products = this.wishlist.map((w) => w.product || w).filter(Boolean)
        }
      } catch (error) {
        console.error('Error loading wishlist:', error)
      } finally {
        this.loading = false
      }
    },
    async updateWishlist(productId, action = 'add') {
      this.loadingProductId = productId
      try {
        const response = await this.$axios.post('/api/update-wishlist', {
          product_id: productId,
          action: action,
        })
        if (response.data.success) {
          await this.loadWishlist()
        }
      } catch (error) {
        console.error('Error updating wishlist:', error)
      } finally {
        this.loadingProductId = null
      }
    },
    async moveToCart(product) {
      const cartStore = useCartStore()
      if (cartStore.addToCart) {
        await cartStore.addToCart({ product_id: product.id, quantity: 1 })
      } else {
        await this.$axios.post('/api/update-cart', { product_id: product.id, action: 'increase' })
      }
      await this.updateWishlist(product.id, 'remove')
    },
  },
}
</script>
