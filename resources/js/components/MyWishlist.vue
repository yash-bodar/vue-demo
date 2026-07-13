<template>
  <div class="products-container">
    <!-- Header Section -->
    <div class="products-header bg-white m-2 border-bottom shadow-sm p-3 mx-4 my-3 rounded-2">
      <div class="row align-items-center g-3">
        <div class="col-12 col-lg-4">
          <div class="d-flex align-items-center">
            <div class="me-3">
              <i class="fas fa-bookmark fa-2x text-primary"></i>
            </div>
            <div>
              <h5 class="mb-0 fw-bold text-primary">Wishlist</h5>
              <p class="mb-0 text-muted small">Your favorite products - {{ productCount }} items</p>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!-- Products Grid -->
    <div class="products-body p-4">
      <!-- Loading State -->
      <div v-if="loading" class="text-center py-5">
        <div class="spinner-border text-primary" role="status">
          <span class="visually-hidden">Loading...</span>
        </div>
        <p class="mt-3 text-muted">Loading products...</p>
      </div>

      <!-- Empty State -->
      <div v-else-if="products.length === 0" class="text-center py-5">
        <i class="fas fa-bookmark fa-4x text-muted mb-3"></i>
        <h5 class="text-muted">No products found</h5>
        <p class="text-muted">Please add products to your wishlist</p>
      </div>

      <!-- Products Grid -->
      <div v-else class="row g-4">
        <div v-for="(product, index) in products" class="col-md-6 col-lg-4 col-xl-3" :key="index">
          <div class="card h-100 product-card border-0 rounded-3 overflow-hidden shadow-sm">
            <div class="product-image-container position-relative">
              <div class="image-placeholder d-flex align-items-center justify-content-center bg-light">
                <img :src="this.$getImageUrl(product.image)" class="product-image" :alt="product.name">
                <button type="button" class="position-absolute bottom-0 mb-2 end-0 me-2 fs-4 wishlist-btn" :disabled="loadingProductId == product.id" @click="updateWishlist(product.id, 'remove')"><i class="fas fa-heart wish-icon active"></i></button>
              </div>
            </div>
            <div class="card-body d-flex flex-column p-3">
              <div class="product-info cursor-pointer" @click="$router.push(`/product/detail/${product.id}`)">
                <h5 class="product-title fw-semibold mb-2">{{ product.name }}</h5>
                <p class="product-description text-muted small mb-3"> {{ product.description || 'No description available' }} </p>
              </div>
            </div>
          </div>
        </div>

        <div class="card-footer bg-white border-0 p-4" v-if="lastPage > 1">
          <div class="pagination-wrapper">
            <div class="pagination-info text-center mb-3">
              <span class="text-muted">Showing <strong>{{ (currentPage - 1) * perPage + 1 }}</strong> to <strong>{{
                Math.min(currentPage * perPage, productCount) }}</strong> of <strong>{{ productCount }}</strong>Orders</span>
            </div>
            <nav aria-label="Orders pagination">
              <ul class="pagination justify-content-center mb-0 gap-2">
                <li class="page-item" :class="{ disabled: currentPage === 1 }">
                  <button class="page-link rounded-3 color-primary" style="min-width: 42px; height: 42px;"
                    :disabled="currentPage === 1" @click="loadWishlist(currentPage - 1)">
                    <i class="fas fa-chevron-left"></i>
                  </button>
                </li>
                <li v-for="page in lastPage" :key="page" class="page-item" :class="{ active: page === currentPage }">
                  <button class="page-link rounded-3" style="min-width: 42px; height: 42px;"
                    :class="page === currentPage ? 'bg-primary text-white' : 'color-primary'" @click="loadWishlist(page)">
                    {{ page }}
                  </button>
                </li>
                <li class="page-item" :class="{ disabled: currentPage === lastPage }">
                  <button class="page-link rounded-3 color-primary" style="min-width: 42px; height: 42px;"
                    :disabled="currentPage === lastPage" @click="loadWishlist(currentPage + 1)">
                    <i class="fas fa-chevron-right"></i>
                  </button>
                </li>
              </ul>
            </nav>
          </div>
        </div>

      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'WishList',
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
      filters: {}
    }
  },
  mounted() {
    this.loadWishlist();
  },
  methods: {
    async loadWishlist(page = 1) {
      this.loading = true;
      try {
        const params = new URLSearchParams({
            page: page,
            ...this.filters
        });
        const response = await this.$axios.get(`/api/get-wishlist?${params.toString()}`);
        const data = response.data;
        if (data.success) {
          this.wishlist = data.data.data;
          this.productCount = data.data.total;
          this.currentPage = data.data.current_page;
          this.lastPage = data.data.last_page;
          this.perPage = data.data.per_page;
          this.products = [];
          this.wishlist.forEach(wishlistItem => {
            this.products.push(wishlistItem.product);
          });
        }
      } catch (error) {
        console.error('Error loading wishlist:', error);
      } finally {
        this.loading = false;
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
          this.loadWishlist();
        }
      } catch (error) {
        console.error('Error updating wishlist:', error);
      } finally {
        this.loadingProductId = null;
      }
    },
  }
}
</script>
