<template>
  <div class="card card-vuexy p-4">
    <!-- Header Controls -->
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-4 pb-3 border-bottom">
      <div class="d-flex align-items-center gap-3">
        <div class="badge bg-label-primary rounded-3 p-3">
          <i class="fas fa-box fs-3"></i>
        </div>
        <div>
          <h4 class="mb-0 fw-bold text-heading">Products Management</h4>
          <small class="text-muted">Total {{ productCount }} products in store catalog</small>
        </div>
      </div>

      <div class="d-flex align-items-center gap-2 flex-wrap">
        <button class="btn btn-sm btn-label-secondary" @click="exportData('pdf')" title="Export PDF">
          <i class="fas fa-file-pdf text-danger me-1"></i> PDF
        </button>
        <button class="btn btn-sm btn-label-secondary" @click="exportData('csv')" title="Export CSV">
          <i class="fas fa-file-csv text-success me-1"></i> CSV
        </button>
        <router-link to="/products/create" class="btn btn-primary rounded-pill px-4 shadow-primary">
          <i class="fas fa-plus me-1"></i>Add Product
        </router-link>
      </div>
    </div>

    <!-- Filter Toolbar -->
    <div class="row g-3 mb-4">
      <div class="col-12 col-md-4">
        <div class="input-group">
          <span class="input-group-text bg-transparent border-end-0 text-muted"><i class="fas fa-search"></i></span>
          <input type="search" class="form-control border-start-0 ps-0" v-model="filters.search" placeholder="Search product name..." />
        </div>
      </div>
      <div class="col-6 col-md-3">
        <select class="form-select" v-model="filters.category_id" @change="fetchProducts(1)">
          <option value="">All Categories</option>
          <option v-for="category in categories" :key="category.id" :value="category.id">{{ category.name }}</option>
        </select>
      </div>
      <div class="col-6 col-md-3">
        <select class="form-select" v-model="filters.status" @change="fetchProducts(1)">
          <option value="">All Status</option>
          <option value="Active">Active</option>
          <option value="Inactive">Inactive</option>
        </select>
      </div>
      <div class="col-6 col-md-2">
        <select class="form-select" v-model="filters.currency" @change="fetchProducts(1)">
          <option value="">All Currencies</option>
          <option value="USD">USD ($)</option>
          <option value="EUR">EUR (€)</option>
          <option value="GBP">GBP (£)</option>
          <option value="INR">INR (₹)</option>
        </select>
      </div>
    </div>

    <!-- Table Section -->
    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0">
        <thead>
          <tr>
            <th @click="sortByField('name', 'fetchProducts')" class="cursor-pointer">Product</th>
            <th @click="sortByField('price', 'fetchProducts')" class="cursor-pointer">Price</th>
            <th @click="sortByField('stock', 'fetchProducts')" class="cursor-pointer">Stock</th>
            <th @click="sortByField('category_id', 'fetchProducts')" class="cursor-pointer">Category</th>
            <th @click="sortByField('status', 'fetchProducts')" class="cursor-pointer">Status</th>
            <th @click="sortByField('created_at', 'fetchProducts')" class="cursor-pointer">Created</th>
            <th class="text-end">Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr v-if="productsList.length > 0" v-for="product in productsList" :key="product.id">
            <td class="cursor-pointer" @click="$router.push(`/product/detail/${product.id}`)">
              <div class="d-flex align-items-center gap-3">
                <img class="rounded-3 border" width="44" height="44" style="object-fit: cover;" :src="getImageUrl(product.image)" :alt="product.name" />
                <div>
                  <div class="fw-bold text-heading fs-7">{{ product.name }}</div>
                  <small class="text-muted fs-8">ID: #{{ product.id }}</small>
                </div>
              </div>
            </td>
            <td class="fw-bold text-primary fs-7">
              {{ product.currency_sign }} {{ product.price }}
            </td>
            <td>
              <span class="badge" :class="product.stock <= 0 ? 'bg-label-danger' : product.stock <= 10 ? 'bg-label-warning' : 'bg-label-success'">
                {{ product.stock }} Units
              </span>
            </td>
            <td>
              <span class="badge bg-label-info fs-8">{{ product.category?.name || 'General' }}</span>
            </td>
            <td>
              <span class="badge" :class="product.status === 'Active' ? 'bg-label-success' : 'bg-label-secondary'">
                {{ product.status }}
              </span>
            </td>
            <td class="fs-8 text-muted">{{ formatDate(product.created_at) }}</td>
            <td class="text-end">
              <div class="d-inline-flex gap-1">
                <router-link :to="`/products/edit/${product.id}`" class="btn btn-sm btn-icon btn-label-primary" title="Edit">
                  <i class="fas fa-pen-to-square fs-8"></i>
                </router-link>
                <button type="button" class="btn btn-sm btn-icon btn-label-danger" @click="deleteProduct(product.id)" title="Delete">
                  <i class="fas fa-trash-can fs-8"></i>
                </button>
              </div>
            </td>
          </tr>
          <tr v-else>
            <td colspan="7" class="text-center py-5 text-muted">
              <i class="fas fa-box-open fa-3x mb-3 opacity-50"></i>
              <h6>No products found matching filters</h6>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Pagination Footer -->
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 pt-4 border-top mt-3" v-if="lastPage > 1">
      <small class="text-muted fs-8">Showing {{ (currentPage - 1) * perPage + 1 }} to {{ Math.min(currentPage * perPage, productCount) }} of {{ productCount }} entries</small>
      <nav>
        <ul class="pagination mb-0 gap-1">
          <li class="page-item" :class="{ disabled: currentPage === 1 }">
            <button class="page-link rounded-circle border-0 text-primary" :disabled="currentPage === 1" @click="fetchProducts(currentPage - 1)">
              <i class="fas fa-chevron-left fs-8"></i>
            </button>
          </li>
          <li v-for="page in lastPage" :key="page" class="page-item" :class="{ active: page === currentPage }">
            <button class="page-link rounded-circle border-0 fw-bold" :class="page === currentPage ? 'bg-primary text-white' : 'text-primary'" @click="fetchProducts(page)">
              {{ page }}
            </button>
          </li>
          <li class="page-item" :class="{ disabled: currentPage === lastPage }">
            <button class="page-link rounded-circle border-0 text-primary" :disabled="currentPage === lastPage" @click="fetchProducts(currentPage + 1)">
              <i class="fas fa-chevron-right fs-8"></i>
            </button>
          </li>
        </ul>
      </nav>
    </div>
  </div>
</template>

<script>
import Swal from 'sweetalert2'
import { sortByField, getSortIcon } from '../utils/table'
import { getImageUrl } from '../utils/ImageUrl'
import { formatDate } from '../utils/formatDate'
import { showSwalMessage, confirmAction } from '../utils/showMessage'

export default {
  name: 'Products',
  data() {
    return {
      productsList: [],
      productCount: 0,
      categories: [],
      currentPage: 1,
      lastPage: 1,
      perPage: 10,
      filters: {
        search: '',
        category_id: '',
        currency: '',
        status: ''
      },
      sort_field: 'id',
      sort_direction: 'desc',
      searchTimeout: null
    }
  },
  watch: {
    'filters.search'() {
      if (this.searchTimeout) clearTimeout(this.searchTimeout)
      this.searchTimeout = setTimeout(() => this.fetchProducts(1), 300)
    }
  },
  mounted() {
    this.fetchProducts(1)
    this.fetchCategories()
  },
  methods: {
    getImageUrl,
    formatDate,
    sortByField,
    getSortIcon,
    async fetchProducts(page = 1) {
      try {
        const params = new URLSearchParams({
          page: page,
          sort_field: this.sort_field,
          sort_direction: this.sort_direction,
          ...this.filters
        })
        const response = await this.$axios.get(`/api/products?${params.toString()}`)
        const data = response.data.data
        this.productsList = data.data || []
        this.productCount = data.total || 0
        this.currentPage = data.current_page || 1
        this.lastPage = data.last_page || 1
        this.perPage = data.per_page || 10
      } catch (err) {
        console.error('Fetch products error:', err)
      }
    },
    async fetchCategories() {
      try {
        const response = await this.$axios.get('/api/get-categories')
        if (response.data.success) {
          this.categories = response.data.data || []
        }
      } catch (err) {
        console.error('Fetch categories error:', err)
      }
    },
    async deleteProduct(id) {
      const confirmed = await confirmAction('Delete Product', 'Are you sure you want to delete this product?')
      if (!confirmed) return
      try {
        const response = await this.$axios.delete(`/api/products/${id}`)
        if (response.data.success) {
          showSwalMessage('Product deleted successfully', 'success')
          await this.fetchProducts(this.currentPage)
        }
      } catch (err) {
        showSwalMessage('Failed to delete product', 'error')
      }
    },
    exportData(type) {
      window.open(`/api/products/export?type=${type}`, '_blank')
    }
  }
}
</script>
