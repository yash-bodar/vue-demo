<template>
  <div class="card card-vuexy p-4">
    <!-- Header Section -->
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-4 pb-3 border-bottom">
      <div class="d-flex align-items-center gap-3">
        <div class="badge bg-label-info rounded-3 p-3">
          <i class="fas fa-tags fs-3"></i>
        </div>
        <div>
          <h4 class="mb-0 fw-bold text-heading">Categories Management</h4>
          <small class="text-muted">Total {{ categoriesCount }} categories in store</small>
        </div>
      </div>

      <router-link to="/categories/create" class="btn btn-primary rounded-pill px-4 shadow-primary">
        <i class="fas fa-plus me-1"></i>Add Category
      </router-link>
    </div>

    <!-- Filter Bar -->
    <div class="row g-3 mb-4">
      <div class="col-12 col-md-6">
        <div class="input-group">
          <span class="input-group-text bg-transparent border-end-0 text-muted"><i class="fas fa-search"></i></span>
          <input type="search" class="form-control border-start-0 ps-0" v-model="filters.search" placeholder="Search category name..." />
        </div>
      </div>
      <div class="col-12 col-md-6">
        <select class="form-select" v-model="filters.status" @change="fetchCategories(1)">
          <option value="">All Status</option>
          <option value="Active">Active</option>
          <option value="Inactive">Inactive</option>
        </select>
      </div>
    </div>

    <!-- Table -->
    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0">
        <thead>
          <tr>
            <th @click="sortByField('name', 'fetchCategories')" class="cursor-pointer">Category Name</th>
            <th @click="sortByField('status', 'fetchCategories')" class="cursor-pointer">Status</th>
            <th @click="sortByField('products_count', 'fetchCategories')" class="cursor-pointer">Products Count</th>
            <th @click="sortByField('created_at', 'fetchCategories')" class="cursor-pointer">Created Date</th>
            <th class="text-end">Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr v-if="categoriesList.length > 0" v-for="category in categoriesList" :key="category.id">
            <td>
              <div class="fw-bold text-heading fs-7">{{ category.name }}</div>
              <small class="text-muted fs-8">ID: #{{ category.id }}</small>
            </td>
            <td>
              <span class="badge" :class="category.status === 'Active' ? 'bg-label-success' : 'bg-label-secondary'">
                {{ category.status }}
              </span>
            </td>
            <td>
              <span class="badge bg-label-primary fs-8">
                <i class="fas fa-box me-1"></i>{{ category.products_count ?? 0 }} Items
              </span>
            </td>
            <td class="fs-8 text-muted">{{ formatDate(category.created_at) }}</td>
            <td class="text-end">
              <div class="d-inline-flex gap-1">
                <router-link :to="`/categories/edit/${category.id}`" class="btn btn-sm btn-icon btn-label-primary" title="Edit">
                  <i class="fas fa-pen-to-square fs-8"></i>
                </router-link>
                <button type="button" class="btn btn-sm btn-icon btn-label-danger" @click="deleteCategory(category.id)" title="Delete">
                  <i class="fas fa-trash-can fs-8"></i>
                </button>
              </div>
            </td>
          </tr>
          <tr v-else>
            <td colspan="5" class="text-center py-5 text-muted">
              <i class="fas fa-folder-open fa-3x mb-3 opacity-50"></i>
              <h6>No categories found</h6>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Pagination -->
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 pt-4 border-top mt-3" v-if="lastPage > 1">
      <small class="text-muted fs-8">Showing {{ (currentPage - 1) * perPage + 1 }} to {{ Math.min(currentPage * perPage, categoriesCount) }} of {{ categoriesCount }} entries</small>
      <nav>
        <ul class="pagination mb-0 gap-1">
          <li class="page-item" :class="{ disabled: currentPage === 1 }">
            <button class="page-link rounded-circle border-0 text-primary" :disabled="currentPage === 1" @click="fetchCategories(currentPage - 1)">
              <i class="fas fa-chevron-left fs-8"></i>
            </button>
          </li>
          <li v-for="page in lastPage" :key="page" class="page-item" :class="{ active: page === currentPage }">
            <button class="page-link rounded-circle border-0 fw-bold" :class="page === currentPage ? 'bg-primary text-white' : 'text-primary'" @click="fetchCategories(page)">
              {{ page }}
            </button>
          </li>
          <li class="page-item" :class="{ disabled: currentPage === lastPage }">
            <button class="page-link rounded-circle border-0 text-primary" :disabled="currentPage === lastPage" @click="fetchCategories(currentPage + 1)">
              <i class="fas fa-chevron-right fs-8"></i>
            </button>
          </li>
        </ul>
      </nav>
    </div>
  </div>
</template>

<script>
import { sortByField, getSortIcon } from '../utils/table'
import { formatDate } from '../utils/formatDate'

export default {
  name: 'Categories',
  data() {
    return {
      categoriesList: [],
      categoriesCount: 0,
      currentPage: 1,
      lastPage: 1,
      perPage: 10,
      filters: {
        status: '',
        sort_by: 'id',
        sort_order: 'desc',
        search: ''
      },
      searchTimeout: null
    }
  },
  watch: {
    'filters.search'() {
      if (this.searchTimeout) clearTimeout(this.searchTimeout)
      this.searchTimeout = setTimeout(() => this.fetchCategories(1), 300)
    }
  },
  mounted() {
    this.fetchCategories()
  },
  methods: {
    formatDate,
    sortByField,
    getSortIcon,
    fetchCategories(page = 1) {
      const params = new URLSearchParams({
        page: page,
        ...this.filters
      })
      this.$axios.get(`/api/categories?${params.toString()}`)
        .then(res => {
          this.categoriesList = res.data.data.data
          this.categoriesCount = res.data.data.total
          this.currentPage = res.data.data.current_page
          this.lastPage = res.data.data.last_page
          this.perPage = res.data.data.per_page
        })
        .catch(err => console.error('Failed to fetch categories', err))
    },
    deleteCategory(id) {
      if (!confirm('Are you sure you want to delete this category?')) return
      this.$axios.delete(`/api/categories/${id}`)
        .then(res => {
          if (res.data.success) {
            this.fetchCategories(this.currentPage)
          }
        })
        .catch(err => console.error('Failed to delete category', err))
    }
  }
}
</script>