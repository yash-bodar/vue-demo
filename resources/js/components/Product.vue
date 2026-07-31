<template>
  <div class="container-xl py-4">
    <!-- Header Section (Title & Search Filter Controls) -->
    <div class="card card-vuexy mb-4 p-4">
      <div class="row align-items-center g-3">
        <!-- Title & Subtitle -->
        <div class="col-12 col-lg-4">
          <div class="d-flex align-items-center gap-3">
            <div class="badge bg-label-primary rounded-3 p-3">
              <i class="fas fa-store fs-3"></i>
            </div>
            <div>
              <h4 class="mb-0 fw-bold text-heading">Store Catalog</h4>
              <small class="text-muted">Discover & filter premium products</small>
            </div>
          </div>
        </div>

        <!-- Controls Row -->
        <div class="col-12 col-lg-8">
          <div class="d-flex align-items-center gap-2 justify-content-lg-end flex-wrap">
            <!-- Search Autocomplete Input -->
            <div class="position-relative flex-grow-1 search-wrapper-max-320">
              <div class="input-group">
                <span class="input-group-text bg-transparent border-end-0 text-muted"
                  ><i class="fas fa-search"></i
                ></span>
                <input
                  type="search"
                  class="form-control border-start-0 ps-0"
                  :value="filters.searchQuery"
                  @input="handleSearchInput"
                  @blur="closeSuggestions"
                  @focus="showSuggestions = suggestions.length > 0"
                  placeholder="Search products..."
                  autocomplete="off"
                />
              </div>

              <!-- Floating Autocomplete Suggestions -->
              <div
                v-if="showSuggestions"
                class="position-absolute w-100 bg-card rounded-3 shadow-lg border border-light mt-1 py-1 search-suggest-popover"
              >
                <div v-if="autocompleteLoading" class="text-center py-3 text-muted fs-8">
                  <i class="fas fa-circle-notch fa-spin me-1 text-primary"></i>Searching catalog...
                </div>
                <template v-else>
                  <div
                    v-for="item in suggestions"
                    :key="item.id"
                    class="d-flex align-items-center gap-2 p-2 hover-bg-light cursor-pointer border-bottom border-light"
                    @mousedown="selectSuggestion(item.id)"
                  >
                    <img :src="getImageUrl(item.image)" class="rounded-2 product-thumb-36" />
                    <div class="flex-grow-1 min-w-0">
                      <div class="text-heading fw-bold fs-7 text-truncate mb-0">
                        {{ item.name }}
                      </div>
                      <div class="text-primary fw-bold fs-8">
                        {{ user?.currency_sign
                        }}{{ (item.converted_price || item.price || 0).toFixed(2) }}
                      </div>
                    </div>
                  </div>
                </template>
              </div>
            </div>

            <!-- Sort By Select -->
            <div class="dropdown-menu-200">
              <select class="form-select" v-model="selectedSort">
                <option value="">Sort By</option>
                <option v-for="sort in sortingList" :key="sort.value" :value="sort.value">
                  {{ sort.label }}
                </option>
              </select>
            </div>

            <!-- Toggle Filter Panel Button -->
            <button
              type="button"
              class="btn px-3"
              :class="
                showAdvancedFilters || activeFiltersCount > 0
                  ? 'btn-primary'
                  : 'btn-outline-primary'
              "
              @click="showAdvancedFilters = !showAdvancedFilters"
            >
              <i class="fas fa-sliders me-1"></i>
              <span>Filters</span>
              <span
                v-if="activeFiltersCount > 0"
                class="badge bg-white text-primary rounded-pill ms-1"
                >{{ activeFiltersCount }}</span
              >
            </button>
          </div>
        </div>
      </div>

      <!-- Expandable Advanced Filters Drawer -->
      <transition name="fade">
        <div v-if="showAdvancedFilters" class="mt-4 pt-4 border-top border-light">
          <!-- Categories Filter Pills -->
          <div class="row align-items-center g-3 mb-3">
            <div class="col-12 col-md-3">
              <span class="text-muted fs-8 fw-bold text-uppercase">
                <i class="fas fa-tags me-1 text-primary"></i>Categories
              </span>
            </div>
            <div class="col-12 col-md-9 d-flex flex-wrap gap-2">
              <button
                type="button"
                class="btn btn-sm rounded-pill px-3"
                :class="!filters.selectedCategory ? 'btn-primary' : 'btn-label-secondary'"
                @click="filters.selectedCategory = ''"
              >
                All Categories
              </button>
              <button
                v-for="category in categories"
                :key="category.id"
                type="button"
                class="btn btn-sm rounded-pill px-3"
                :class="
                  filters.selectedCategory === category.id ? 'btn-primary' : 'btn-label-secondary'
                "
                @click="filters.selectedCategory = category.id"
              >
                {{ category.name }}
              </button>
            </div>
          </div>

          <!-- Price Range Slider & Inputs -->
          <div class="row align-items-center g-3">
            <div class="col-12 col-md-3">
              <span class="text-muted fs-8 fw-bold text-uppercase">
                <i class="fas fa-dollar-sign me-1 text-primary"></i>Price Range
              </span>
            </div>
            <div class="col-12 col-md-9">
              <div class="d-flex align-items-center gap-3 flex-wrap">
                <div class="input-group input-group-sm input-group-max-130">
                  <span class="input-group-text bg-light text-muted fs-8">Min</span>
                  <input
                    type="number"
                    class="form-control"
                    v-model.number="filters.minPrice"
                    placeholder="0"
                    min="0"
                  />
                </div>
                <span class="text-muted">—</span>
                <div class="input-group input-group-sm input-group-max-130">
                  <span class="input-group-text bg-light text-muted fs-8">Max</span>
                  <input
                    type="number"
                    class="form-control"
                    v-model.number="filters.maxPrice"
                    placeholder="Max"
                    min="0"
                  />
                </div>
                <button
                  type="button"
                  class="btn btn-sm btn-label-danger"
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

    <!-- Products Grid & Loading State -->
    <div class="products-body">
      <!-- Loading Skeleton -->
      <div v-if="loading" class="row g-4">
        <div v-for="n in 8" :key="n" class="col-sm-6 col-md-6 col-lg-4 col-xl-3">
          <div class="card card-vuexy p-3">
            <div class="skeleton-box mb-3 skeleton-h-220"></div>
            <div class="skeleton-box py-2 mb-2 w-75"></div>
            <div class="skeleton-box py-2 w-50"></div>
          </div>
        </div>
      </div>

      <!-- Empty State -->
      <div v-else-if="products.length === 0" class="text-center py-5 card card-vuexy">
        <i class="fas fa-box-open fa-4x text-muted mb-3 opacity-50"></i>
        <h5 class="fw-bold text-heading">No Products Found</h5>
        <p class="text-muted fs-7">Try refining your search keyword or selected category filter.</p>
      </div>

      <!-- Product Cards Grid -->
      <div v-else>
        <div class="row g-4">
          <div
            v-for="(product, index) in products"
            :key="index"
            class="col-sm-6 col-md-6 col-lg-4 col-xl-3"
          >
            <div class="card card-vuexy h-100 d-flex flex-column">
              <!-- Image Container -->
              <div class="product-card-img-wrapper">
                <img
                  :src="getImageUrl(product.image)"
                  class="product-card-img"
                  :alt="product.name"
                />
                <div class="product-card-overlay">
                  <router-link
                    :to="`/product/detail/${product.id}`"
                    class="btn btn-sm btn-light rounded-circle btn-icon shadow"
                  >
                    <i class="fas fa-eye text-primary"></i>
                  </router-link>
                  <button
                    type="button"
                    class="btn btn-sm btn-light rounded-circle btn-icon shadow"
                    :disabled="loadingProductId == product.id"
                    @click="updateWishlist(product.id, product.wishlist ? 'remove' : 'add')"
                  >
                    <i
                      class="fas fa-heart"
                      :class="product.wishlist ? 'text-danger' : 'text-muted'"
                    ></i>
                  </button>
                </div>
                <span
                  v-if="product.stock <= 0"
                  class="position-absolute top-0 start-0 m-2 badge bg-danger rounded-pill fs-9"
                >
                  Out of Stock
                </span>
              </div>

              <!-- Product Info Body -->
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

                <!-- Star Rating -->
                <div class="d-flex align-items-center justify-content-between mb-3 fs-8">
                  <div>
                    <template v-for="i in 5" :key="i">
                      <i class="fas fa-star text-warning" v-if="i <= product.average_rating"></i>
                      <i class="far fa-star text-warning" v-else></i>
                    </template>
                  </div>
                  <span class="text-primary fw-bold">{{ product.average_rating || 0 }}/5</span>
                </div>

                <!-- Price & Cart Action -->
                <div
                  class="mt-auto pt-2 border-top border-light d-flex align-items-center justify-content-between"
                >
                  <span class="fw-bold text-primary fs-5">
                    {{ user?.currency_sign || '$'
                    }}{{ (product.converted_price || product.price || 0).toFixed(2) }}
                  </span>

                  <!-- Cart Controls -->
                  <div>
                    <div
                      v-if="getCartQuantity(product.id) > 0"
                      class="d-flex align-items-center justify-content-between border rounded-pill p-1 bg-light"
                    >
                      <button
                        type="button"
                        class="btn btn-sm btn-icon border-0 p-0 text-primary"
                        :disabled="loadingProductId == product.id"
                        @click="updateCart(product.id, 'decrease')"
                      >
                        <i class="fas fa-minus fs-9"></i>
                      </button>
                      <span class="fw-bold fs-7">{{ getCartQuantity(product.id) }}</span>
                      <button
                        type="button"
                        class="btn btn-sm btn-icon border-0 p-0 text-primary"
                        :disabled="
                          getCartQuantity(product.id) >= product.stock ||
                          loadingProductId === product.id
                        "
                        @click="updateCart(product.id, 'increase')"
                      >
                        <i class="fas fa-plus fs-9"></i>
                      </button>
                    </div>

                    <button
                      v-else-if="product.stock > 0"
                      type="button"
                      :disabled="loadingProductId == product.id"
                      class="btn btn-sm btn-primary rounded-pill w-100"
                      @click="updateCart(product.id, 'increase')"
                    >
                      <i class="fas fa-cart-plus me-1"></i>Add
                    </button>

                    <button
                      v-else
                      type="button"
                      class="btn btn-sm btn-secondary rounded-pill w-100 fs-8"
                      disabled
                    >
                      Sold Out
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Pagination Section -->
        <div class="mt-5 d-flex justify-content-center" v-if="lastPage > 1">
          <div class="card card-vuexy p-3 px-4">
            <div class="d-flex flex-column align-items-center gap-2">
              <span class="text-muted fs-8">
                Showing <strong>{{ (currentPage - 1) * perPage + 1 }}</strong> to
                <strong>{{ Math.min(currentPage * perPage, productCount) }}</strong> of
                <strong>{{ productCount }}</strong> items
              </span>
              <nav>
                <ul class="pagination mb-0 gap-2">
                  <li class="page-item" :class="{ disabled: currentPage === 1 }">
                    <button
                      class="page-link rounded-circle border-0 text-primary"
                      :disabled="currentPage === 1"
                      @click="loadProducts(currentPage - 1)"
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
                      @click="loadProducts(page)"
                    >
                      {{ page }}
                    </button>
                  </li>
                  <li class="page-item" :class="{ disabled: currentPage === lastPage }">
                    <button
                      class="page-link rounded-circle border-0 text-primary"
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
import { useWishlistStore } from '../stores/wishlist'
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
      sortingList: [
        { value: 'name_asc', label: 'Name (A-Z)' },
        { value: 'name_desc', label: 'Name (Z-A)' },
        { value: 'price_asc', label: 'Price (Low to High)' },
        { value: 'price_desc', label: 'Price (High to Low)' },
        { value: 'created_at_asc', label: 'Created At (Oldest)' },
        { value: 'created_at_desc', label: 'Created At (Newest)' },
      ],
    }
  },
  computed: {
    ...mapState(useAuthStore, ['user']),
    activeFiltersCount() {
      let count = 0
      if (this.filters.selectedCategory) count++
      if (this.filters.minPrice) count++
      if (this.filters.maxPrice) count++
      return count
    },
  },
  watch: {
    'filters.selectedCategory'() {
      this.loadProducts(1)
    },
    'filters.searchQuery'() {
      this.loadProducts(1)
    },
    'filters.minPrice'() {
      this.triggerPriceFilter()
    },
    'filters.maxPrice'() {
      this.triggerPriceFilter()
    },
    selectedSort() {
      if (this.selectedSort) {
        this.filters.sort_by = this.selectedSort.split('_')[0]
        this.filters.sort_order = this.selectedSort.split('_')[1]
      }
      this.loadProducts(1)
    },
  },
  mounted() {
    if (this.$route.query.category) {
      this.filters.selectedCategory = this.$route.query.category
    }
    if (this.$route.query.search) {
      this.filters.searchQuery = this.$route.query.search
    }
    this.loadProducts(1)
    this.getCategories()
    this.loadCart()
    this.loadWishlist()
  },
  methods: {
    getImageUrl,
    async loadProducts(page = 1) {
      this.loading = true
      try {
        const params = new URLSearchParams({
          page: page,
          ...this.filters,
        })
        const response = await this.$axios.get(`/api/get-products?${params.toString()}`)
        const data = response.data
        if (data.success) {
          this.products = data.data.data
          this.productCount = data.data.total
          this.currentPage = data.data.current_page
          this.lastPage = data.data.last_page
          this.perPage = data.data.per_page
        }
      } catch (error) {
        console.error('Error loading products:', error)
      } finally {
        this.loading = false
      }
    },
    handleSearchInput(event) {
      const query = event.target.value
      this.filters.searchQuery = query

      if (this.searchTimeout) clearTimeout(this.searchTimeout)
      if (query.trim().length >= 2) {
        this.searchTimeout = setTimeout(() => this.fetchSuggestions(query), 300)
      } else {
        this.suggestions = []
        this.showSuggestions = false
      }
    },
    async fetchSuggestions(query) {
      this.autocompleteLoading = true
      this.showSuggestions = true
      try {
        const response = await this.$axios.get(`/api/get-products?searchQuery=${query}&perPage=5`)
        if (response.data.success) {
          this.suggestions = response.data.data.data
        }
      } catch (err) {
        console.error('Autocomplete error:', err)
      } finally {
        this.autocompleteLoading = false
      }
    },
    selectSuggestion(id) {
      this.showSuggestions = false
      this.$router.push(`/product/detail/${id}`)
    },
    closeSuggestions() {
      setTimeout(() => {
        this.showSuggestions = false
      }, 200)
    },
    triggerPriceFilter() {
      if (this.priceTimeout) clearTimeout(this.priceTimeout)
      this.priceTimeout = setTimeout(() => this.loadProducts(1), 500)
    },
    resetPriceFilter() {
      this.filters.minPrice = ''
      this.filters.maxPrice = ''
      this.loadProducts(1)
    },
    async getCategories() {
      try {
        const response = await this.$axios.get('/api/get-categories')
        if (response.data.success) {
          this.categories = response.data.data
        }
      } catch (err) {
        console.error('Categories error:', err)
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
    async loadWishlist() {
      try {
        const response = await this.$axios.get('/api/get-wishlist')
        if (response.data.success) {
          this.wishlist = response.data.data
        }
      } catch (err) {
        console.error('Wishlist load error:', err)
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
          // YB - 31-07-2026: Mutate local product wishlist state for instant UI reflection
          const product = this.products.find((p) => p.id === productId)
          if (product) {
            product.wishlist = action === 'add' ? { id: productId } : null
          }
          await this.loadWishlist()
          // YB - 31-07-2026: Refresh global wishlist store to update header count badge
          const wishlistStore = useWishlistStore()
          await wishlistStore.fetchWishlist()
        }
      } catch (err) {
        console.error('Wishlist update error:', err)
      } finally {
        this.loadingProductId = null
      }
    },
  },
}
</script>
