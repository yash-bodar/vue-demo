<template>
  <div class="container py-4">
    <!-- Header Section (Search & Filter Card) -->
    <div class="card card-premium mb-4 p-4">
      <div class="row align-items-center g-3">
        <!-- Logo / Title -->
        <div class="col-12 col-lg-4">
          <div class="d-flex align-items-center">
            <div class="me-3 d-inline-flex align-items-center justify-content-center rounded-3 bg-primary text-white" style="width: 48px; height: 48px;">
              <i class="fas fa-shopping-bag fs-4"></i>
            </div>
            <div>
              <h5 class="mb-0 fw-bold text-dark">Explore Products</h5>
              <p class="mb-0 text-muted small">Find your perfect item</p>
            </div>
          </div>
        </div>
        
        <!-- Controls Row -->
        <div class="col-12 col-lg-8">
          <div class="d-flex align-items-center gap-2 justify-content-lg-end flex-wrap">
            <!-- Search Input -->
            <div class="position-relative flex-grow-1" style="max-width: 320px; min-width: 200px;">
              <div class="search-input-wrapper">
                <i class="fas fa-search search-icon"></i>
                <input 
                  type="search" 
                  class="form-control" 
                  :value="filters.searchQuery" 
                  @input="handleSearchInput"
                  @blur="closeSuggestions"
                  @focus="showSuggestions = suggestions.length > 0"
                  placeholder="Search products..."
                  autocomplete="off"
                >
              </div>
              
              <!-- Floating Autocomplete Suggestions -->
              <div v-if="showSuggestions" class="position-absolute w-100 bg-white rounded-3 shadow-lg border border-light mt-1 py-1" style="z-index: 1000; top: 100%; left: 0; max-height: 280px; overflow-y: auto;">
                <div v-if="autocompleteLoading" class="text-center py-3 text-muted small">
                  <i class="fas fa-circle-notch fa-spin me-1 text-primary"></i>Searching...
                </div>
                <template v-else>
                  <div 
                    v-for="item in suggestions" 
                    :key="item.id" 
                    class="d-flex align-items-center gap-2 p-2 hover-bg-light cursor-pointer border-bottom border-light last-border-0"
                    @mousedown="selectSuggestion(item.id)"
                  >
                    <img :src="getImageUrl(item.image)" class="rounded-1" style="width: 32px; height: 32px; object-fit: cover;">
                    <div class="flex-grow-1 min-w-0">
                      <div class="text-dark fw-bold small text-truncate mb-0" style="font-size: 0.85rem;">{{ item.name }}</div>
                      <div class="text-primary fw-bold" style="font-size: 0.75rem;">{{ user?.currency_sign }}{{ item.converted_price.toFixed(2) }}</div>
                    </div>
                  </div>
                </template>
              </div>
            </div>
            
            <!-- Sort Filter -->
            <div style="min-width: 200px;">
              <select class="form-select" v-model="selectedSort" v-select2="{ placeholder: 'Sort By', allowClear: false }">
                <option value="">Sort By</option>
                <option v-for="sort in sortingList" :key="sort.value" :value="sort.value">{{ sort.label }}</option>
              </select>
            </div>

            <!-- Toggle Advanced Filters Button -->
            <button 
              type="button" 
              class="btn btn-sm px-3 py-2 d-flex align-items-center gap-2"
              :class="showAdvancedFilters || activeFiltersCount > 0 ? 'btn-primary' : 'btn-outline-primary'"
              @click="showAdvancedFilters = !showAdvancedFilters"
            >
              <i class="fas fa-filter"></i>
              <span>Filters</span>
              <span v-if="activeFiltersCount > 0" class="badge bg-white text-primary rounded-pill small px-2">{{ activeFiltersCount }}</span>
            </button>
          </div>
        </div>
      </div>

      <!-- Expandable Advanced Filters Drawer -->
      <transition name="slide-fade">
        <div v-if="showAdvancedFilters" class="mt-4 pt-4 border-top border-light">
          <!-- Category Chips Selector -->
          <div class="row align-items-center g-3 mb-4">
            <div class="col-12 col-md-3">
              <span class="text-muted small fw-bold text-uppercase d-block" style="font-size: 0.7rem; letter-spacing: 0.05em;">
                <i class="fas fa-tags me-1 text-primary"></i>Categories
              </span>
            </div>
            <div class="col-12 col-md-9 d-flex flex-wrap gap-2">
              <button 
                type="button" 
                class="category-chip"
                :class="!filters.selectedCategory ? 'active' : ''"
                @click="filters.selectedCategory = ''"
              >
                All Categories
              </button>
              <button 
                v-for="category in categories" 
                :key="category.id" 
                type="button" 
                class="category-chip"
                :class="filters.selectedCategory === category.id ? 'active' : ''"
                @click="filters.selectedCategory = category.id"
              >
                {{ category.name }}
              </button>
            </div>
          </div>

          <!-- Price Range Slider & Inputs -->
          <div class="row align-items-center g-3">
            <div class="col-12 col-md-3">
              <span class="text-muted small fw-bold text-uppercase d-block" style="font-size: 0.7rem; letter-spacing: 0.05em;">
                <i class="fas fa-sliders-h me-1 text-primary"></i>Price Range Limit
              </span>
            </div>
            <div class="col-12 col-md-9">
              <div class="d-flex align-items-center gap-3 flex-wrap">
                <!-- Min Price Input -->
                <div class="input-group input-group-sm" style="max-width: 120px;">
                  <span class="input-group-text bg-light text-muted small" style="font-size: 0.75rem;">Min</span>
                  <input 
                    type="number" 
                    class="form-control" 
                    v-model.number="filters.minPrice" 
                    placeholder="0"
                    min="0"
                  >
                </div>
                <span class="text-muted">—</span>
                <!-- Max Price Input -->
                <div class="input-group input-group-sm" style="max-width: 120px;">
                  <span class="input-group-text bg-light text-muted small" style="font-size: 0.75rem;">Max</span>
                  <input 
                    type="number" 
                    class="form-control" 
                    v-model.number="filters.maxPrice" 
                    placeholder="Max"
                    min="0"
                  >
                </div>
                <!-- Max Price Slider -->
                <div class="flex-grow-1 d-flex align-items-center gap-2" style="min-width: 200px;">
                  <input 
                    type="range" 
                    class="form-range" 
                    v-model.number="filters.maxPrice" 
                    min="0" 
                    max="1000"
                    step="10"
                  >
                  <span class="text-muted small text-nowrap" style="font-size: 0.75rem;">
                    Limit: {{ filters.maxPrice ? user?.currency_sign + filters.maxPrice : 'None' }}
                  </span>
                </div>
                <!-- Reset Filters -->
                <button 
                  type="button" 
                  class="btn btn-light btn-sm text-primary fw-bold" 
                  @click="resetPriceFilter"
                  :disabled="!filters.minPrice && !filters.maxPrice"
                >
                  Reset Price
                </button>
              </div>
            </div>
          </div>
        </div>
      </transition>
    </div>

    <!-- Products Grid / Body -->
    <div class="products-body">
      <!-- Loading State -->
      <div v-if="loading" class="text-center py-5">
        <div class="spinner-border text-primary" role="status">
          <span class="visually-hidden">Loading...</span>
        </div>
        <p class="mt-3 text-muted">Loading premium products...</p>
      </div>

      <!-- Empty State -->
      <div v-else-if="products.length === 0" class="text-center py-5 bg-white rounded-4 shadow-sm border border-light">
        <i class="fas fa-box-open fa-4x text-muted mb-3 opacity-60"></i>
        <h5 class="text-muted fw-bold">No Products Found</h5>
        <p class="text-muted small">Try adjusting your search query or filters</p>
      </div>

      <!-- Products Grid -->
      <div v-else>
        <div class="row g-4">
          <div v-for="(product, index) in products" class="col-sm-6 col-md-6 col-lg-4 col-xl-3" :key="index">
            <div class="card card-premium h-100 d-flex flex-column">
              <!-- Image Section -->
              <div class="premium-product-img-wrapper">
                <img :src="getImageUrl(product.image)" class="premium-product-img" :alt="product.name">
                
                <!-- Wishlist Floating Toggle -->
                <button 
                  type="button" 
                  class="wishlist-float-btn" 
                  :disabled="loadingProductId == product.id" 
                  @click="updateWishlist(product.id, product.wishlist ? 'remove' : 'add')"
                >
                  <i class="fas fa-heart" :class="{ 'active': product.wishlist }"></i>
                </button>
              </div>

              <!-- Content Section -->
              <div class="card-body d-flex flex-column p-3">
                <div class="product-info cursor-pointer" @click="$router.push(`/product/detail/${product.id}`)">
                  <h6 class="product-title fw-bold mb-1" style="min-height: auto; max-height: 40px; overflow: hidden; text-overflow: ellipsis; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical;">
                    {{ product.name }}
                  </h6>
                  <p class="text-muted small mb-2" style="display: -webkit-box; -webkit-line-clamp: 1; -webkit-box-orient: vertical; overflow: hidden; min-height: auto;">
                    {{ product.description || 'No description available' }}
                  </p>
                </div>

                <!-- Rating -->
                <div class="d-flex align-items-center justify-content-between mb-3">
                  <div class="small">
                    <template v-for="i in 5" :key="i">
                      <i class="fas fa-star text-warning" v-if="i <= product.average_rating"></i>
                      <i class="far fa-star text-warning" v-else></i>
                    </template>
                  </div>
                  <span class="text-primary fw-bold small">{{ product.average_rating || 0 }}/5</span>
                </div>

                <!-- Actions / Price Bottom Row -->
                <div class="mt-auto pt-2 border-top border-light d-flex align-items-center justify-content-between">
                  <span class="fw-extrabold text-primary fs-5">
                    {{ user?.currency_sign }}{{ product.converted_price.toFixed(2) }}
                  </span>

                  <!-- Cart Buttons -->
                  <div class="cart-controls" style="min-width: 110px;">
                    <div v-if="getCartQuantity(product.id) > 0" class="premium-qty-controls justify-content-between">
                      <button 
                        type="button" 
                        class="qty-btn" 
                        :disabled="loadingProductId == product.id" 
                        @click="updateCart(product.id, 'decrease')"
                      >
                        <i class="fas fa-minus small"></i>
                      </button>
                      <span class="qty-display small">{{ getCartQuantity(product.id) }}</span>
                      <button 
                        type="button" 
                        class="qty-btn" 
                        :disabled="getCartQuantity(product.id) >= product.stock || loadingProductId === product.id" 
                        @click="updateCart(product.id, 'increase')"
                      >
                        <i class="fas fa-plus small"></i>
                      </button>
                    </div>
                    
                    <button 
                      type="button" 
                      v-else-if="product.stock > 0" 
                      :disabled="loadingProductId == product.id" 
                      class="btn btn-primary btn-sm w-100 rounded-3 py-2" 
                      @click="updateCart(product.id, 'increase')"
                    >
                      <i class="fas fa-cart-plus me-1"></i>Add
                    </button>
                    
                    <button 
                      type="button" 
                      v-else class="btn btn-light btn-sm w-100 border text-muted py-2" 
                      disabled
                    >
                      Out of Stock
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Pagination Section -->
        <div class="mt-5 d-flex justify-content-center" v-if="lastPage > 1">
          <div class="card card-premium p-3 px-4">
            <div class="d-flex flex-column align-items-center gap-2">
              <span class="text-muted small">
                Showing <strong>{{ (currentPage - 1) * perPage + 1 }}</strong> to <strong>{{ Math.min(currentPage * perPage, productCount) }}</strong> of <strong>{{ productCount }}</strong> products
              </span>
              <nav aria-label="Products pagination">
                <ul class="pagination mb-0 gap-2">
                  <li class="page-item" :class="{ disabled: currentPage === 1 }">
                    <button 
                      class="page-link rounded-circle border-0 d-flex align-items-center justify-content-center" 
                      style="width: 36px; height: 36px; color: var(--primary-color);"
                      :disabled="currentPage === 1" 
                      @click="loadProducts(currentPage - 1)"
                    >
                      <i class="fas fa-chevron-left"></i>
                    </button>
                  </li>
                  <li v-for="page in lastPage" :key="page" class="page-item" :class="{ active: page === currentPage }">
                    <button 
                      class="page-link rounded-circle border-0 d-flex align-items-center justify-content-center" 
                      style="width: 36px; height: 36px; font-weight: 600;"
                      :class="page === currentPage ? 'bg-primary text-white' : 'color-primary'" 
                      @click="loadProducts(page)"
                    >
                      {{ page }}
                    </button>
                  </li>
                  <li class="page-item" :class="{ disabled: currentPage === lastPage }">
                    <button 
                      class="page-link rounded-circle border-0 d-flex align-items-center justify-content-center" 
                      style="width: 36px; height: 36px; color: var(--primary-color);"
                      :disabled="currentPage === lastPage" 
                      @click="loadProducts(currentPage + 1)"
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
    </div>
  </div>
</template>

<script>
import { mapState } from 'pinia'
import { useAuthStore } from '../stores/auth'
import { getImageUrl } from '../utils/ImageUrl'

export default {
  name: 'Product',
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
        minPrice: '',
        maxPrice: '',
        sort_by: 'id',
        sort_order: 'desc',
      },
      selectedSort: '',
      showAdvancedFilters: false,
      suggestions: [],
      showSuggestions: false,
      autocompleteLoading: false,
      searchTimeout: null,
      priceTimeout: null,
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
  computed: {
    ...mapState(useAuthStore, ['user']),
    activeFiltersCount() {
      let count = 0;
      if (this.filters.selectedCategory) count++;
      if (this.filters.minPrice) count++;
      if (this.filters.maxPrice) count++;
      return count;
    }
  },
  watch: {
    'filters.selectedCategory'() {
      this.loadProducts(1);
    },
    'filters.searchQuery'() {
      this.loadProducts(1);
    },
    'filters.minPrice'() {
      this.triggerPriceFilter();
    },
    'filters.maxPrice'() {
      this.triggerPriceFilter();
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
    getImageUrl,
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
    handleSearchInput(event) {
      const query = event.target.value;
      if (this.searchTimeout) {
        clearTimeout(this.searchTimeout);
      }
      
      if (!query || query.length < 2) {
        this.suggestions = [];
        this.showSuggestions = false;
        this.filters.searchQuery = '';
        return;
      }
      
      this.searchTimeout = setTimeout(async () => {
        this.autocompleteLoading = true;
        this.filters.searchQuery = query;
        
        try {
          const params = new URLSearchParams({
            searchQuery: query,
            per_page: 5
          });
          const response = await this.$axios.get(`/api/get-products?${params.toString()}`);
          if (response.data && response.data.success) {
            this.suggestions = response.data.data.data;
            this.showSuggestions = this.suggestions.length > 0;
          }
        } catch (error) {
          console.error('Autocomplete error:', error);
        } finally {
          this.autocompleteLoading = false;
        }
      }, 400);
    },
    selectSuggestion(productId) {
      this.showSuggestions = false;
      this.$router.push(`/product/detail/${productId}`);
    },
    closeSuggestions() {
      setTimeout(() => {
        this.showSuggestions = false;
      }, 200);
    },
    triggerPriceFilter() {
      if (this.priceTimeout) {
        clearTimeout(this.priceTimeout);
      }
      this.priceTimeout = setTimeout(() => {
        this.loadProducts(1);
      }, 400);
    },
    resetPriceFilter() {
      this.filters.minPrice = '';
      this.filters.maxPrice = '';
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
        } else {
          if (existing) {
            existing.quantity--;
            if (existing.quantity <= 0) {
              this.cart = this.cart.filter(p => p.product_id !== productId);
            }
          }
        }
      }
      this.loadingProductId = null;
    },
    async updateWishlist(productId, action) {
      this.loadingProductId = productId;
      try {
        const response = await this.$axios.post('/api/update-wishlist', {
          product_id: productId,
          action: action
        });
        const data = response.data;
        if (data.success) {
          if (this.wishlist && this.wishlist.data) {
            if (action === 'add') {
              this.wishlist.data.push({ product_id: productId });
            } else {
              this.wishlist.data = this.wishlist.data.filter(p => p.product_id !== productId);
            }
          }
          this.loadProducts(this.currentPage);
        }
      } catch (error) {
        console.error('Error updating wishlist:', error);
      } finally {
        this.loadingProductId = null;
      }
    },
    async loadCart() {
      const response = await this.$axios.get('/api/get-cart');
      const data = response.data;
      if (data.success) {
        this.cart = data.data;
      }
    },
    async loadWishlist() {
      const response = await this.$axios.get('/api/get-wishlist');
      const data = response.data;
      if (data.success) {
        this.wishlist = data.data;
      }
    },
    async getCategories() {
      const response = await this.$axios.get('/api/get-categories');
      const data = response.data;
      if (data.success) {
        this.categories = data.data;
      }
    },
    getCartQuantity(productId) {
      const item = this.cart.find(p => p.product_id === productId);
      return item ? item.quantity : 0;
    }
  }
}
</script>

<style scoped>
.hover-bg-light:hover {
  background-color: #f8fafc;
}
.last-border-0:last-child {
  border-bottom: 0 !important;
}

/* Category Chips Styles */
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

/* Transition Animations for Filters Drawer */
.slide-fade-enter-active,
.slide-fade-leave-active {
  transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
}

.slide-fade-enter-from,
.slide-fade-leave-to {
  transform: translateY(-10px);
  opacity: 0;
}
</style>
