<template>
  <div class="container-xl py-4">
    <!-- Header with Back Button -->
    <div class="d-flex align-items-center justify-content-between mb-4">
      <div>
        <h4 class="fw-bold text-heading m-0">Product Overview</h4>
        <small class="text-muted">Detailed specifications & reviews</small>
      </div>
      <button class="btn btn-outline-primary rounded-pill px-4" @click="$router.back()">
        <i class="fas fa-arrow-left me-2"></i>Back to Catalog
      </button>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="card card-vuexy p-5 text-center">
      <div class="spinner-border text-primary mx-auto mb-3" role="status"></div>
      <p class="text-muted m-0">Loading product details...</p>
    </div>

    <!-- Main Product Card -->
    <div v-else-if="product" class="card card-vuexy p-4 mb-4">
      <div class="row g-4">
        <!-- Product Image Gallery -->
        <div class="col-md-5">
          <div
            class="product-card-img-wrapper rounded-3 position-relative shadow-sm product-thumb-sq"
          >
            <img :src="getImageUrl(product.image)" class="product-card-img" :alt="product.name" />
            <button
              type="button"
              class="btn btn-light rounded-circle btn-icon shadow position-absolute top-0 end-0 m-3"
              :disabled="loadingProductId == product.id"
              @click="updateWishlist(product.id, product.wishlist ? 'remove' : 'add')"
            >
              <i class="fas fa-heart" :class="product.wishlist ? 'text-danger' : 'text-muted'"></i>
            </button>
          </div>
        </div>

        <!-- Product Information Details -->
        <div class="col-md-7 d-flex flex-column">
          <div class="mb-2">
            <span class="badge bg-label-primary fs-8 me-2">{{
              product.category?.name || 'Category'
            }}</span>
            <span
              class="badge"
              :class="product.status === 'Active' ? 'bg-label-success' : 'bg-label-secondary'"
            >
              {{ product.status }}
            </span>
          </div>

          <h2 class="fw-bold text-heading mb-3">{{ product.name }}</h2>

          <!-- Price & Ratings Row -->
          <div class="d-flex align-items-center gap-3 mb-3">
            <h3 class="fw-bold text-primary m-0" v-if="user?.role == 'user'">
              {{ user?.currency_sign || '$' }}{{ currentPrice.toFixed(2) }}
            </h3>
            <h3 class="fw-bold text-primary m-0" v-else>
              {{ product.currency_sign || '$'
              }}{{
                selectedVariant && selectedVariant.price !== null
                  ? selectedVariant.price
                  : product.price
              }}
            </h3>

            <div class="border-start ps-3 d-flex align-items-center gap-2">
              <div>
                <template v-for="i in 5" :key="i">
                  <i class="fas fa-star text-warning fs-8" v-if="i <= avgRating"></i>
                  <i class="far fa-star text-warning fs-8" v-else></i>
                </template>
              </div>
              <span class="fw-bold text-primary fs-7">{{ avgRating || 0 }}/5</span>
            </div>
          </div>

          <!-- Description -->
          <p class="text-muted mb-4 fs-7 lh-base">
            {{ product.description || 'No detailed description available for this item.' }}
          </p>

          <!-- Variant Selector Chips -->
          <div class="mb-4" v-if="product.variants && product.variants.length > 0">
            <label class="fw-bold fs-7 text-heading mb-2">Available Options:</label>
            <div class="d-flex flex-wrap gap-2">
              <button
                v-for="variant in product.variants"
                :key="variant.id"
                type="button"
                class="btn btn-sm rounded-pill px-3"
                :class="selectedVariantId === variant.id ? 'btn-primary' : 'btn-label-secondary'"
                @click="selectedVariantId = variant.id"
              >
                {{ variant.name }}
                <span class="fs-8 opacity-75 ms-1" v-if="variant.price !== null">
                  ({{ user?.currency_sign }}{{ variant.converted_price.toFixed(2) }})
                </span>
              </button>
            </div>
          </div>

          <!-- Stock Info & Quick Meta -->
          <div class="row g-3 mb-4">
            <div class="col-6">
              <div class="p-3 rounded-3 bg-light border-subtle border">
                <small class="text-muted d-block fs-8">Stock Status</small>
                <span
                  class="fw-bold fs-6"
                  :class="currentStock > 0 ? 'text-success' : 'text-danger'"
                >
                  {{ currentStock > 0 ? `${currentStock} Units Available` : 'Out of Stock' }}
                </span>
              </div>
            </div>
            <div class="col-6">
              <div class="p-3 rounded-3 bg-light border-subtle border">
                <small class="text-muted d-block fs-8">Currency</small>
                <span class="fw-bold fs-6 text-heading">{{ product.currency || 'USD' }}</span>
              </div>
            </div>
          </div>

          <!-- Action Buttons -->
          <div class="mt-auto pt-3 border-top">
            <template v-if="user?.role === 'user'">
              <div
                v-if="getCartQuantity(product.id) > 0"
                class="d-inline-flex align-items-center gap-3 p-2 bg-light rounded-pill border"
              >
                <button
                  type="button"
                  class="btn btn-sm btn-primary rounded-circle btn-icon"
                  :disabled="loadingProductId == product.id"
                  @click="updateCart(product.id, 'decrease')"
                >
                  <i class="fas fa-minus fs-8"></i>
                </button>
                <span class="fw-bold px-2 fs-6">{{ getCartQuantity(product.id) }}</span>
                <button
                  type="button"
                  class="btn btn-sm btn-primary rounded-circle btn-icon"
                  :disabled="
                    getCartQuantity(product.id) >= currentStock || loadingProductId === product.id
                  "
                  @click="updateCart(product.id, 'increase')"
                >
                  <i class="fas fa-plus fs-8"></i>
                </button>
              </div>

              <div v-else-if="currentStock > 0" class="d-flex gap-3">
                <button
                  class="btn btn-primary rounded-pill px-4 py-2.5 shadow-primary fw-bold"
                  @click="updateCart(product.id, 'increase')"
                >
                  <i class="fas fa-cart-shopping me-2"></i>Add to Cart
                </button>
                <router-link
                  to="/checkout"
                  class="btn btn-outline-primary rounded-pill px-4 py-2.5 fw-bold"
                  @click="updateCart(product.id, 'increase')"
                >
                  Buy Now
                </router-link>
              </div>

              <button v-else class="btn btn-secondary rounded-pill px-4 py-2.5" disabled>
                Out of Stock
              </button>
            </template>

            <template v-else-if="user?.role === 'admin'">
              <button
                class="btn btn-primary rounded-pill px-4 py-2.5 shadow-primary fw-bold"
                @click="$router.push(`/products/edit/${product.id}`)"
              >
                <i class="fas fa-pen-to-square me-2"></i>Edit Product Info
              </button>
            </template>
          </div>
        </div>
      </div>
    </div>

    <!-- Ratings & Customer Reviews Section -->
    <div v-if="product" class="card card-vuexy p-4">
      <h5 class="fw-bold mb-4"><i class="fas fa-star text-warning me-2"></i>Ratings & Reviews</h5>

      <!-- Review Form for Customers -->
      <div v-if="user?.role === 'user'" class="p-4 rounded-3 bg-light border-subtle border mb-4">
        <h6 class="fw-bold mb-3">Write a Customer Review</h6>
        <div class="mb-3">
          <label class="form-label fs-7 fw-semibold">Select Rating</label>
          <div class="d-flex gap-2">
            <template v-for="i in 5" :key="i">
              <i
                class="fa-star fs-4 cursor-pointer text-warning"
                :class="i <= newRating.rating ? 'fas' : 'far'"
                @click="newRating.rating = i"
              ></i>
            </template>
          </div>
        </div>
        <div class="mb-3">
          <label class="form-label fs-7 fw-semibold">Your Feedback</label>
          <textarea
            class="form-control"
            v-model="newRating.review"
            rows="3"
            placeholder="Share your experience with this product..."
          ></textarea>
        </div>
        <button
          class="btn btn-primary rounded-pill px-4 fw-bold"
          @click="submitReview"
          :disabled="submitting"
        >
          <i class="fas fa-paper-plane me-2"></i
          >{{ submitting ? 'Submitting...' : 'Submit Review' }}
        </button>
      </div>

      <!-- Reviews List -->
      <div v-if="ratingsLoading" class="text-center py-4">
        <div class="spinner-border text-primary" role="status"></div>
      </div>
      <div v-else-if="ratings.length > 0" class="d-flex flex-column gap-3">
        <div
          v-for="rating in ratings"
          :key="rating.id"
          class="p-3 rounded-3 bg-light border-subtle border"
        >
          <div class="d-flex justify-content-between align-items-start mb-2">
            <div>
              <h6 class="fw-bold m-0 fs-7">{{ rating.user?.name || 'Customer' }}</h6>
              <div class="d-flex gap-1 mt-1">
                <template v-for="i in 5" :key="i">
                  <i class="fas fa-star text-warning fs-9" v-if="i <= rating.rating"></i>
                  <i class="far fa-star text-warning fs-9" v-else></i>
                </template>
              </div>
            </div>
            <small class="text-muted fs-8">{{ formatDate(rating.created_at) }}</small>
          </div>
          <p class="text-muted fs-7 mb-0">{{ rating.review }}</p>
        </div>
      </div>
      <div v-else class="text-muted py-3 text-center fs-7">
        No reviews written yet for this item. Be the first to leave a review!
      </div>
    </div>
  </div>
</template>

<script>
import { mapState } from 'pinia'
import { useAuthStore } from '../stores/auth'
import { useWishlistStore } from '../stores/wishlist'
import { getImageUrl } from '../utils/ImageUrl'

export default {
  name: 'ProductDetail',
  data() {
    return {
      product: null,
      loading: false,
      loadingProductId: null,
      cart: [],
      selectedVariantId: null,
      ratings: [],
      ratingsLoading: false,
      newRating: {
        rating: 5,
        review: '',
      },
      submitting: false,
    }
  },
  computed: {
    ...mapState(useAuthStore, ['user']),
    selectedVariant() {
      if (!this.product || !this.product.variants) return null
      return this.product.variants.find((v) => v.id === this.selectedVariantId) || null
    },
    currentPrice() {
      if (this.selectedVariant && this.selectedVariant.converted_price !== undefined) {
        return this.selectedVariant.converted_price
      }
      return this.product?.converted_price || this.product?.price || 0
    },
    currentStock() {
      if (this.selectedVariant && this.selectedVariant.stock !== undefined) {
        return this.selectedVariant.stock
      }
      return this.product?.stock || 0
    },
    avgRating() {
      if (!this.ratings.length) return this.product?.average_rating || 0
      const sum = this.ratings.reduce((acc, r) => acc + r.rating, 0)
      return Math.round(sum / this.ratings.length)
    },
  },
  async mounted() {
    await this.fetchProduct()
    await this.loadCart()
    await this.fetchRatings()
  },
  methods: {
    getImageUrl,
    formatDate(d) {
      if (!d) return ''
      return new Date(d).toLocaleDateString()
    },
    async fetchProduct() {
      this.loading = true
      try {
        const id = this.$route.params.id
        const response = await this.$axios.get(`/api/product/${id}`)
        if (response.data.success) {
          this.product = response.data.data
          if (this.product.variants && this.product.variants.length > 0) {
            this.selectedVariantId = this.product.variants[0].id
          }
        }
      } catch (err) {
        console.error('Fetch product detail error:', err)
      } finally {
        this.loading = false
      }
    },
    async loadCart() {
      try {
        const response = await this.$axios.get('/api/cart')
        if (response.data.success) {
          this.cart = response.data.data
        }
      } catch (err) {
        console.error('Cart load error:', err)
      }
    },
    async fetchRatings() {
      this.ratingsLoading = true
      try {
        const id = this.$route.params.id
        const response = await this.$axios.get(`/api/product-ratings/${id}`)
        if (response.data.success) {
          this.ratings = response.data.data.data || []
        }
      } catch (err) {
        console.error('Fetch ratings error:', err)
      } finally {
        this.ratingsLoading = false
      }
    },
    getCartQuantity(productId) {
      const item = this.cart.find((c) => c.product_id === productId)
      return item ? item.quantity : 0
    },
    async updateCart(productId, action) {
      this.loadingProductId = productId
      try {
        const response = await this.$axios.post('/api/update-cart', {
          product_id: productId,
          variant_id: this.selectedVariantId,
          action: action,
        })
        if (response.data.success) {
          await this.loadCart()
        }
      } catch (err) {
        console.error('Update cart error:', err)
      } finally {
        this.loadingProductId = null
      }
    },
    async updateWishlist(productId, action) {
      this.loadingProductId = productId
      try {
        const response = await this.$axios.post('/api/update-wishlist', {
          product_id: productId,
          action: action,
        })
        if (response.data.success) {
          // YB - 31-07-2026: Toggle local product wishlist state for detail UI reflection
          this.product.wishlist = action === 'add' ? { id: productId } : null

          // YB - 31-07-2026: Refresh the global Pinia wishlist store to sync header count
          const wishlistStore = useWishlistStore()
          await wishlistStore.fetchWishlist()
        }
      } catch (err) {
        console.error('Wishlist update error:', err)
      } finally {
        this.loadingProductId = null
      }
    },
    async submitReview() {
      if (!this.newRating.review.trim()) return
      this.submitting = true
      try {
        const response = await this.$axios.post('/api/product-ratings', {
          product_id: this.product.id,
          rating: this.newRating.rating,
          review: this.newRating.review,
        })
        if (response.data.success) {
          this.newRating.review = ''
          await this.fetchRatings()
        }
      } catch (err) {
        console.error('Submit review error:', err)
      } finally {
        this.submitting = false
      }
    },
  },
}
</script>
