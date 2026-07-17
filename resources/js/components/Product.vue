<template>
  <div class="products-container">
    <!-- Header Section -->
    <div class="products-header bg-white m-2 border-bottom shadow-sm p-3 mx-4 my-3 rounded-2">
      <div class="row align-items-center g-3">
        <div class="col-12 col-lg-4">
          <div class="d-flex align-items-center">
            <div class="me-3">
              <i class="fas fa-shopping-bag fa-2x text-primary p-1"></i>
            </div>
            <div>
              <h5 class="mb-0 fw-bold text-primary">Products</h5>
              <p class="mb-0 text-muted small">Browse our collection</p>
            </div>
          </div>
        </div>
        <div class="col-12 col-lg-8">
          <div class="row g-2">
            <div class="col-12 col-md-4">
              <div class="search-input-wrapper">
                <i class="fas fa-search search-icon"></i>
                <input type="search" class="form-control ps-5" v-model="filters.searchQuery" placeholder="Search products...">
              </div>
            </div>
            <div class="col-12 col-md-5">
              <select class="form-select" v-model="selectedSort" v-select2="{ placeholder: 'Sort', allowClear: false  }">
                <option value="">Sort</option>
                <option v-for="sort in sortingList" :key="sort.value" :value="sort.value">{{ sort.label }}</option>
              </select>
            </div>
            <div class="col-12 col-md-3">
              <select class="form-select" v-model="filters.selectedCategory" v-select2="{ placeholder: 'All Categories', allowClear: false  }">
                <option value="">All Categories</option>
                <option v-if="categories.length === 0" disabled>Loading categories...</option>
                <option v-else v-for="category in categories" :key="category.id" :value="category.id">{{ category.name }}</option>
              </select>
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
        <i class="fas fa-box-open fa-4x text-muted mb-3"></i>
        <h5 class="text-muted">No products found</h5>
        <p class="text-muted">Try adjusting your search or filter criteria</p>
      </div>

      <!-- Products Grid -->
      <div v-else class="row g-4">
        <div v-for="(product, index) in products" class="col-md-6 col-lg-4 col-xl-3" :key="index">
          <div class="card h-100 product-card border-0 rounded-3 overflow-hidden shadow-sm">
            <div class="product-image-container position-relative">
              <div class="image-placeholder d-flex align-items-center justify-content-center bg-light">
                <img :src="getImageUrl(product.image)" class="product-image" :alt="product.name">
                <button type="button" class="position-absolute bottom-0 mb-2 end-0 me-2 fs-4 wishlist-btn" :disabled="loadingProductId == product.id" @click="updateWishlist(product.id, product.wishlist ? 'remove' : 'add')"><i class="fas fa-heart wish-icon" :class="product.wishlist ? 'active' : ''"></i></button>
              </div>
            </div>
            <div class="card-body d-flex flex-column p-3">
              <div class="product-info cursor-pointer" @click="$router.push(`/product/detail/${product.id}`)">
                <h5 class="product-title fw-semibold mb-2">{{ product.name }}</h5>
                <p class="product-description text-muted small mb-3">
                  {{ product.description || 'No description available' }}
                </p>
              </div>
              <div class="rating-section py-1">
                <div class="d-flex align-items-center justify-content-between">
                  <div class="">
                    <template v-for="i in 5" :key="i">
                      <i class="fas fa-star text-warning" v-if="i <= product.average_rating"></i>
                      <i class="far fa-star text-warning" v-else></i>
                    </template>
                  </div>
                  <span class="text-primary fw-bold me-1">{{ product.average_rating || 0 }}/5</span>
                </div>
              </div>
              <div class="product-actions mt-auto">
                <div class="price-row d-flex justify-content-between align-items-center mb-3">
                  <span class="product-price fs-5 fw-bold text-primary">{{ user?.currency_sign }}{{ product.converted_price.toFixed(2) }}</span>
                  <div class="cart-controls">
                    <div v-if="getCartQuantity(product.id) > 0" class="quantity-controls d-flex align-items-center gap-4 justify-content-center">
                      <button type="button" class="btn btn-sm btn-primary quantity-btn" :disabled="loadingProductId == product.id" @click="updateCart(product.id, 'decrease')">
                        <i class="fas fa-minus"></i>
                      </button>
                      <span class="quantity-display fw-semibold px-2">{{ getCartQuantity(product.id) }}</span>
                      <button type="button" class="btn btn-sm btn-primary quantity-btn" :disabled="getCartQuantity(product.id) >= product.stock || loadingProductId === product.id" @click="updateCart(product.id, 'increase')">
                        <i class="fas fa-plus"></i>
                      </button>
                    </div>
                    <button type="button" v-else-if="product.stock > 0" :disabled="loadingProductId == product.id" class="btn btn-primary add-to-cart-btn w-100" @click="updateCart(product.id, 'increase')">
                      <i class="fas fa-cart-plus me-2"></i>Add to Cart
                    </button>
                    <button type="button" v-else class="btn btn-light border text-muted w-100" disabled>
                      <i class="fas fa-times-circle me-2"></i>Out of Stock
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        
        <div class="card-footer bg-white border-0 p-4" v-if="lastPage > 1">
          <div class="pagination-wrapper">
            <div class="pagination-info text-center mb-3">
              <span class="text-muted">Showing <strong>{{ (currentPage - 1) * perPage + 1 }}</strong> to <strong>{{
                Math.min(currentPage * perPage, productCount) }}</strong> of <strong>{{ productCount }}</strong> Products</span>
            </div>
            <nav aria-label="Products pagination">
              <ul class="pagination justify-content-center mb-0 gap-2">
                <li class="page-item" :class="{ disabled: currentPage === 1 }">
                  <button class="page-link rounded-3 color-primary" style="min-width: 42px; height: 42px;"
                    :disabled="currentPage === 1" @click="loadProducts(currentPage - 1)">
                    <i class="fas fa-chevron-left"></i>
                  </button>
                </li>
                <li v-for="page in lastPage" :key="page" class="page-item" :class="{ active: page === currentPage }">
                  <button class="page-link rounded-3" style="min-width: 42px; height: 42px;"
                    :class="page === currentPage ? 'bg-primary text-white' : 'color-primary'" @click="loadProducts(page)">
                    {{ page }}
                  </button>
                </li>
                <li class="page-item" :class="{ disabled: currentPage === lastPage }">
                  <button class="page-link rounded-3 color-primary" style="min-width: 42px; height: 42px;"
                    :disabled="currentPage === lastPage" @click="loadProducts(currentPage + 1)">
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
import { mapState } from 'pinia'
import { useAuthStore } from '../stores/auth'
import { getImageUrl } from '../utils/ImageUrl'

export default {
  name: 'Product',
  computed: {
    ...mapState(useAuthStore, ['user']),
  },
  data() {
    return {
      products: [],
      categories: [],
      cart: [],
      loadingProductId: null,
      loading: false,
      wishlist: [],
      productCount: 0,
      currentPage: 1,
      perPage: 8,
      lastPage: 1,
      filters: {
        selectedCategory: '',
        searchQuery: '',
        sort_by: 'id',
        sort_order: 'desc',
      },
      selectedSort: '',
      sortingList:[
        {value: 'name_asc', label: 'Name (A-Z)'},
        {value: 'name_desc', label: 'Name (Z-A)'},
        {value: 'price_asc', label: 'Price (Low to High)'},
        {value: 'price_desc', label: 'Price (High to Low)'},
        {value: 'created_at_asc', label: 'Created At (Oldest)'},
        {value: 'created_at_desc', label: 'Created At (Newest)'}
      ]
    }
  },
  watch: {
    'filters.selectedCategory'() {
      this.loadProducts(1);
    },
    'filters.searchQuery'() {
      this.loadProducts(1);
    },
    selectedSort() {
      if (this.selectedSort) {
        this.filters.sort_by = this.selectedSort.split('_')[0];
        this.filters.sort_order = this.selectedSort.split('_')[1];
      }
      this.loadProducts(1);
    },
    wishlist() {
      this.loadProducts(1);
    }
  },
  mounted() {
    this.loadProducts(1);
    this.getCategories();
    this.loadCart();
    this.loadWishlist();
  },
  methods: {
    async loadProducts(page = 1) {
      this.loading = true;
      try {
        const params = new URLSearchParams({
            page: page,
            ...this.filters
        });
        const response = await this.$axios.get(`/api/get-products/?${params.toString()}`);
        const data = response.data;
        if (data.success) {
          this.products = data.data.data;
          this.productCount = data.data.total;
          this.currentPage = data.data.current_page;
          this.lastPage = data.data.last_page;
          this.perPage = data.data.per_page;
        }
      } catch (error) {
        console.error('Error loading products:', error);
      } finally {
        this.loading = false;
      }
    },
    async updateCart(productId, action = 'increase') {
      this.loadingProductId = productId;
      const response = await this.$axios.post('/api/update-cart', {
        product_id: productId,
        action: action
      });
      const data = response.data;
      if (data.success) {
        const existing = this.cart.find(p => p.product_id === productId);

        if (action === 'increase') {
          if (existing) {
            existing.quantity++;
          } else {
            this.cart.push({ product_id: productId, quantity: 1 });
          }
        }
        if (action === 'decrease') {
          if (existing) {
            if (existing.quantity > 1) {
              existing.quantity--;
            } else {
              this.cart = this.cart.filter(p => p.product_id !== productId);
            }
          }
        }
      }
      this.loadingProductId = null;
    },
    async loadWishlist() {
      try {
        const response = await this.$axios.get('/api/fetch-wishlist');
        const data = response.data;
        if (data.success) {
          this.wishlist = data.data;
        }
      } catch (error) {
        console.error('Error loading wishlist:', error);
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
        }
      } catch (error) {
        console.error('Error updating wishlist:', error);
      } finally {
        this.loadingProductId = null;
      }
    },
    async getCategories() {
      const response = await this.$axios.get('/api/get-categories');
      const data = response.data;
      if (data.success) {
        this.categories = data.data;
      }
    },
    async loadCart() {
      const response = await this.$axios.get('/api/cart');
      const data = response.data;
      if (data.success) {
        this.cart = data.data;
      }
    },
    getCartQuantity(productId) {
      const item = this.cart.find(p => p.product_id === productId);
      return item ? item.quantity : 0;
    },
    getImageUrl
  }
}
</script>
