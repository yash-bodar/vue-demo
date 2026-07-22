<template>
  <div class="products-container">
    <!-- Header -->
    <div class="product-header bg-white m-2 border-bottom shadow-sm p-3 mx-4 my-3 rounded-2">
      <div class="row align-items-center g-3">
        <div class="col-12 col-md-6">
          <div class="d-flex align-items-center">
            <div class="me-3">
              <i class="fas fa-box fa-2x text-primary"></i>
            </div>
            <div>
              <h5 class="mb-0 fw-bold text-dark">Product Details</h5>
              <p class="mb-0 text-muted small">View product information</p>
            </div>
          </div>
        </div>
        <div class="col-12 col-md-6">
          <div class="d-flex justify-content-end">
            <button class="btn btn-outline-primary" @click="$router.back()">
              <i class="fas fa-arrow-left me-2"></i>Back
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Body -->
    <div class="product-body p-4">
      <!-- Loading State -->
      <div v-if="loading" class="text-center py-5">
        <div class="spinner-border text-primary" role="status">
          <span class="visually-hidden">Loading...</span>
        </div>
        <p class="mt-3 text-muted">Loading product details...</p>
      </div>

      <!-- Product Content -->
      <div v-else-if="product" class="card shadow-sm rounded-3 border-0 overflow-hidden">
        <div class="card-body p-4">
          <div class="row">
            <!-- Product Image -->
            <div class="col-md-5 mb-4 mb-md-0">
              <div class="product-image-container position-relative">
                <img :src="getImageUrl(product.image)" class="img-fluid rounded-3" :alt="product.name">
                <button type="button" class="position-absolute bottom-0 mb-2 end-0 me-2 fs-4 wishlist-btn" :disabled="loadingProductId == product.id" @click="updateWishlist(product.id, product.wishlist ? 'remove' : 'add')"><i class="fas fa-heart wish-icon" :class="product.wishlist ? 'active' : ''"></i></button>
              </div>
            </div>

            <!-- Product Info -->
            <div class="col-md-7">
              <h3 class="fw-bold mb-3">{{ product.name }}</h3>

              <div class="mb-3">
                <span class="badge ms-2" :class="product.status === 'Active' ? 'bg-success' : 'bg-secondary'">
                  {{ product.status }}
                </span>
                <span class="badge bg-primary ms-2">{{ product.category?.name || 'Uncategorized' }}</span>
              </div>

              <div class="price-section mb-2">
                <h4 class="fw-bold text-primary mb-0" v-if="user?.role == 'user'">
                  {{ user?.currency_sign || '$' }}{{ currentPrice.toFixed(2) }}
                </h4>
                <h4 class="fw-bold text-primary mb-0" v-else>
                  {{ product.currency_sign || '$' }}{{ (selectedVariant && selectedVariant.price !== null ? selectedVariant.price : product.price) }}
                </h4>
              </div>

              <div class="d-flex align-items-center gap-2 mb-3">
                <div class="">
                  <template v-for="i in 5" :key="i">
                    <i class="fas fa-star text-warning" v-if="i <= avgRating"></i>
                    <i class="far fa-star text-warning" v-else></i>
                  </template>
                </div>
                <span class="text-primary fw-bold me-1">{{ avgRating || 0 }}/5</span>
              </div>

              <!-- Variant Selector Chips -->
              <div class="mb-4" v-if="product.variants && product.variants.length > 0">
                <h6 class="fw-semibold mb-2">Select Options</h6>
                <div class="d-flex flex-wrap gap-2">
                  <button 
                    v-for="variant in product.variants" 
                    :key="variant.id" 
                    type="button" 
                    class="category-chip" 
                    :class="selectedVariantId === variant.id ? 'active' : ''"
                    @click="selectedVariantId = variant.id"
                  >
                    {{ variant.name }}
                    <span class="small opacity-75 ms-1" v-if="variant.price !== null">
                      ({{ user?.currency_sign }}{{ variant.converted_price.toFixed(2) }})
                    </span>
                  </button>
                </div>
              </div>

              <div class="mb-4">
                <h6 class="fw-semibold mb-2">Description</h6>
                <p class="text-muted">{{ product.description || 'No description available' }}</p>
              </div>

              <div class="row mb-4">
                <div class="col-md-6 mt-1">
                  <div class="info-box p-3 bg-light rounded-3">
                    <small class="text-muted d-block mb-1">Available Quantity</small>
                    <span class="fw-bold fs-5">{{ currentStock }}</span>
                  </div>
                </div>
                <div class="col-md-6 mt-1">
                  <div class="info-box p-3 bg-light rounded-3">
                    <small class="text-muted d-block mb-1">Currency</small>
                    <span class="fw-bold fs-5">{{ product.currency }}</span>
                  </div>
                </div>

                <div class="action-buttons col-md-6 mt-2" v-if="user?.role === 'user'">
                  <div v-if="getCartQuantity(product.id) > 0"
                    class="quantity-controls d-flex align-items-center justify-content-center gap-4">
                    <button type="button" class="btn btn-sm btn-primary quantity-btn"
                      :disabled="loadingProductId == product.id" @click="updateCart(product.id, 'decrease')">
                      <i class="fas fa-minus"></i>
                    </button>
                    <span class="quantity-display fw-semibold px-2">{{ getCartQuantity(product.id) }}</span>
                    <button type="button" class="btn btn-sm btn-primary quantity-btn"
                      :disabled="getCartQuantity(product.id) >= currentStock || loadingProductId === product.id"
                      @click="updateCart(product.id, 'increase')">
                      <i class="fas fa-plus"></i>
                    </button>
                  </div>
                  <button v-else-if="currentStock > 0" class="btn btn-primary bg-primary-linear rounded-2 px-4"
                    @click="updateCart(product.id, 'increase')">
                    <i class="fas fa-shopping-cart me-2"></i>Add to Cart
                  </button>
                  <button v-else class="btn btn-secondary rounded-2 px-4" disabled>
                    <i class="fas fa-shopping-cart me-2"></i>Out of Stock
                  </button>
                </div>
                <div class="action-buttons col-md-6 mt-2" v-else-if="user?.role === 'admin'">
                  <button class="btn btn-primary bg-primary rounded-2 px-4"
                    @click="$router.push(`/products/edit/${product.id}`)">
                    <i class="fas fa-edit me-2"></i>Edit Product
                  </button>
                </div>

              </div>

            </div>
          </div>
        </div>
      </div>

      <!-- Ratings & Reviews Section -->
      <div v-if="product" class="card shadow-sm rounded-3 border-0 overflow-hidden mt-4" id="aaa">
        <div class="card-header bg-white py-3 border-bottom">
          <h5 class="mb-0 fw-semibold"><i class="fas fa-star text-warning me-2"></i>Ratings & Reviews</h5>
        </div>
        <div class="card-body p-4">
          <!-- Add Review Form -->
          <div class="review-form mb-4 p-4 bg-light rounded-3" v-if="user?.role === 'user'">
            <h6 class="fw-semibold mb-3">Write a Review</h6>
            <div class="mb-3">
              <label class="form-label fw-semibold">Rating</label>
              <div class="d-flex gap-2">
                <template v-for="i in 5" :key="i">
                  <i class="fa-star fs-4 cursor-pointer text-warning"
                    :class="i <= newRating.rating ? 'fas' : 'far'" @click="newRating.rating = i"></i>
                </template>
              </div>
            </div>
            <div class="mb-3">
              <label class="form-label fw-semibold">Review</label>
              <textarea class="form-control" v-model="newRating.review" rows="3"
                placeholder="Share your experience with this product..."></textarea>
            </div>
            <button class="btn btn-primary bg-primary-linear rounded-2 px-4" @click="submitReview"
              :disabled="submitting">
              <i class="fas fa-paper-plane me-2"></i>{{ submitting ? 'Submitting...' : 'Submit Review' }}
            </button>
          </div>

          <!-- Reviews List -->
          <div v-if="ratingsLoading" class="text-center py-5">
            <div class="spinner-border text-primary" role="status">
              <span class="visually-hidden">Loading...</span>
            </div>
          </div>
          <div v-else-if="ratings.length > 0">
            <div v-for="rating in ratings" :key="rating.id" class="review-item mb-3 p-3 bg-light rounded-3">
              <div class="d-flex justify-content-between align-items-start mb-2">
                <div>
                  <h6 class="fw-semibold mb-1">{{ rating.user?.name || 'Anonymous' }}</h6>
                  <div class="d-flex">
                    <template v-for="i in 5" :key="i">
                      <i class="fas fa-star text-warning small" v-if="i <= rating.rating"></i>
                      <i class="far fa-star text-warning small" v-else></i>
                    </template>
                  </div>
                </div>
                <small class="text-muted">{{ formatDate(rating.created_at) }}</small>
              </div>
              <div class="d-flex justify-content-between align-items-start mb-2">
                <p class="text-muted mb-0">{{ rating.review || 'No review text' }}</p>
                <button type="button" :disabled="loadingRatingId == rating.id" @click="deleteRating(rating.id)" v-if="user?.role === 'admin'" class="btn btn-sm btn-primary bg-primary"><i class="fas fa-trash small"></i></button>
              </div>    
            </div>

            <!-- Pagination -->
            <div v-if="ratingsLastPage > 1" class="pagination-wrapper mt-4">
              <nav aria-label="Ratings pagination">
                <ul class="pagination justify-content-center mb-0 gap-2">
                  <li class="page-item" :class="{ disabled: ratingsCurrentPage === 1 }">
                    <button class="page-link rounded-3 color-primary" style="min-width: 42px; height: 42px;"
                      :disabled="ratingsCurrentPage === 1" @click="loadRatings(ratingsCurrentPage - 1)">
                      <i class="fas fa-chevron-left"></i>
                    </button>
                  </li>
                  <li v-for="page in ratingsLastPage" :key="page" class="page-item"
                    :class="{ active: page === ratingsCurrentPage }">
                    <button class="page-link rounded-3" style="min-width: 42px; height: 42px;"
                      :class="page === ratingsCurrentPage ? 'bg-primary text-white' : 'color-primary'"
                      @click="loadRatings(page)">
                      {{ page }}
                    </button>
                  </li>
                  <li class="page-item" :class="{ disabled: ratingsCurrentPage === ratingsLastPage }">
                    <button class="page-link rounded-3 color-primary" style="min-width: 42px; height: 42px;"
                      :disabled="ratingsCurrentPage === ratingsLastPage" @click="loadRatings(ratingsCurrentPage + 1)">
                      <i class="fas fa-chevron-right"></i>
                    </button>
                  </li>
                </ul>
              </nav>
            </div>
          </div>
          <div v-else class="text-center py-5">
            <i class="fas fa-comments fa-4x text-muted mb-3"></i>
            <h6 class="text-muted">No reviews yet</h6>
            <p class="text-muted small">Be the first to review this product!</p>
          </div>
        </div>
      </div>

      <!-- Empty State -->
      <div v-else class="text-center py-5">
        <i class="fas fa-box-open fa-4x text-muted mb-3"></i>
        <h5 class="text-muted">Product not found</h5>
        <p class="text-muted mb-4">The product you're looking for doesn't exist.</p>
        <button class="btn btn-primary bg-primary-linear" @click="$router.push('/product')">
          <i class="fas fa-arrow-left me-2"></i>Back to Products
        </button>
      </div>
    </div>
  </div>
</template>

<script>
import { mapState } from 'pinia'
import { useAuthStore } from '../stores/auth'
import { getImageUrl } from '../utils/ImageUrl'
import { formatDate } from '../utils/formatDate'

export default {
  name: 'ProductDetail',
  computed: {
    ...mapState(useAuthStore, ['user']),
    selectedVariant() {
      if (!this.product || !this.product.variants) return null;
      return this.product.variants.find(v => v.id === this.selectedVariantId) || null;
    },
    currentPrice() {
      if (this.selectedVariant) {
        return this.selectedVariant.converted_price;
      }
      return this.product ? this.product.converted_price : 0;
    },
    currentStock() {
      if (this.selectedVariant) {
        return this.selectedVariant.stock;
      }
      return this.product ? this.product.stock : 0;
    }
  },
  data() {
    return {
      product: null,
      loading: false,
      cart: [],
      ratings: [],
      ratingsLoading: false,
      ratingsCurrentPage: 1,
      ratingsCount: 0,
      ratingsPerPage: 5,
      ratingsLastPage: 1,
      selectedVariantId: null,
      newRating: {
        rating: 0,
        review: ''
      },
      loadingProductId: null,
      loadingRatingId: null,
      avgRating: 0,
      submitting: false
    }
  },
  mounted() {
    this.loadProduct();
    this.loadCart();
  },
  methods: {
    async loadProduct() {
      this.loading = true;
      try {
        const id = this.$route.params.id;
        const response = await this.$axios.get(`/api/product/${id}`);
        const data = response.data;
        if (data.success) {
          this.product = data.data;
          this.avgRating = this.product.average_rating || 0;
          if (this.product.variants && this.product.variants.length > 0) {
            this.selectedVariantId = this.product.variants[0].id;
          }
          this.loadRatings(1);
        }
      } catch (error) {
        console.error('Error loading product:', error);
      } finally {
        this.loading = false;
      }
    },
    async loadCart() {
      const response = await this.$axios.get('/api/cart');
      const data = response.data;
      if (data.success) {
        this.cart = data.data;
      }
    },
    async updateCart(productId, action = 'increase') {
      this.loadingProductId = productId;
      const variantId = this.selectedVariantId;
      
      const response = await this.$axios.post('/api/update-cart', {
        product_id: productId,
        product_variant_id: variantId,
        action: action
      });
      const data = response.data;
      if (data.success) {
        const existing = this.cart.find(p => p.product_id === productId && p.product_variant_id === variantId);

        if (action === 'increase') {
          if (existing) {
            existing.quantity++;
          } else {
            this.cart.push({ product_id: productId, product_variant_id: variantId, quantity: 1 });
          }
        }
        if (action === 'decrease') {
          if (existing) {
            if (existing.quantity > 1) {
              existing.quantity--;
            } else {
              this.cart = this.cart.filter(p => !(p.product_id === productId && p.product_variant_id === variantId));
            }
          }
        }
      }
      this.loadingProductId = null;
    },
    getCartQuantity(productId) {
      const variantId = this.selectedVariantId;
      const item = this.cart.find(p => p.product_id === productId && p.product_variant_id === variantId);
      return item ? item.quantity : 0;
    },
    async loadRatings(page = 1) {
      this.ratingsLoading = true;
      try {
        const id = this.$route.params.id;
        const response = await this.$axios.get(`/api/product-ratings/${id}?page=${page}`);
        const data = response.data;
        if (data.success) {
          this.ratings = data.data.data;
          this.ratingsCurrentPage = data.data.current_page;
          this.ratingsCount = data.data.total;
          this.ratingsLastPage = data.data.last_page;
          this.ratingsPerPage = data.data.per_page;
          this.avgRating = data.average_rating;
        }
      } catch (error) {
        console.error('Error loading ratings:', error);
      } finally {
        this.ratingsLoading = false;
      }
    },
    async submitReview() {
      if (this.newRating.rating === 0) {
        alert('Please select a rating');
        return;
      }
      this.submitting = true;
      try {
        const response = await this.$axios.post('/api/product-ratings', {
          product_id: this.product.id,
          rating: this.newRating.rating,
          review: this.newRating.review
        });
        const data = response.data;
        if (data.success) {
          this.newRating = { rating: 0, review: '' };
          this.loadRatings(1);
        }
      } catch (error) {
        console.error('Error submitting review:', error);
      } finally {
        this.submitting = false;
      }
    },
    async updateWishlist(productId, action = 'add') {
      this.loadingProductId = productId;
      try {
        const response = await this.$axios.post('/api/update-wishlist', {
          product_id: productId,
          action: action
        });
        const data = response.data;
        if (data.success) {
          this.wishlist = [...data.data];
          this.product.wishlist = this.wishlist.some(w => w.product_id === productId);
        }
      } catch (error) {
        console.error('Error updating wishlist:', error);
        this.loadingProductId = null;
      } finally {
        this.loadingProductId = null;
      }
    },
    async deleteRating(ratingId){
      this.loadingRatingId = ratingId;
      try {
        if (!confirm('Are you sure you want to delete this rating?')) return
        const response = await this.$axios.delete(`/api/delete-product-rating/${ratingId}`);
        const data = response.data;
        if (data.success) {
          this.loadRatings(this.ratingsCurrentPage);
        }
      } catch (error) {
        console.error('Error deleting rating:', error);
        this.loadingRatingId = null;
      } finally {
        this.loadingRatingId = null;
      }
    },
    getImageUrl,
    formatDate
  }
}
</script>

<style scoped>
.category-chip {
  background-color: #f8fafc;
  color: #64748b;
  border: 1px solid #e2e8f0;
  padding: 0.5rem 1rem;
  border-radius: 2rem;
  font-weight: 600;
  font-size: 0.85rem;
  transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
  cursor: pointer;
}

.category-chip:hover {
  background-color: #cbd5e1;
  color: #334155;
  border-color: #cbd5e1;
}

.category-chip.active {
  background-color: var(--primary-color) !important;
  color: #ffffff !important;
  border-color: var(--primary-color) !important;
  box-shadow: 0 4px 10px rgba(99, 102, 241, 0.25);
}
</style>
