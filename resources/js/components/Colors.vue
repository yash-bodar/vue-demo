<template>
  <div class="card card-vuexy p-4">
    <!-- Header -->
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-4 pb-3 border-bottom">
      <div class="d-flex align-items-center gap-3">
        <div class="badge bg-label-warning rounded-3 p-3">
          <i class="fas fa-palette fs-3"></i>
        </div>
        <div>
          <h4 class="mb-0 fw-bold text-heading">Color Swatches</h4>
          <small class="text-muted">Total {{ colorsCount }} color options</small>
        </div>
      </div>

      <button type="button" class="btn btn-primary rounded-pill px-4 shadow-primary" @click="openModal()">
        <i class="fas fa-plus me-1"></i>Add Color
      </button>
    </div>

    <!-- Filter Bar -->
    <div class="row g-3 mb-4">
      <div class="col-12 col-md-6">
        <div class="input-group">
          <span class="input-group-text bg-transparent border-end-0 text-muted"><i class="fas fa-search"></i></span>
          <input type="search" class="form-control border-start-0 ps-0" v-model="filters.search" placeholder="Search color name or hex..." />
        </div>
      </div>
    </div>

    <!-- Table -->
    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0">
        <thead>
          <tr>
            <th @click="sortByField('name')" class="cursor-pointer">Color Name</th>
            <th>Color Swatch</th>
            <th @click="sortByField('created_at')" class="cursor-pointer">Created Date</th>
            <th class="text-end">Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr v-if="colorsList.length > 0" v-for="color in colorsList" :key="color.id">
            <td>
              <div class="fw-bold text-heading fs-7">{{ color.name }}</div>
              <small class="text-muted fs-8">ID: #{{ color.id }}</small>
            </td>
            <td>
              <div class="d-flex align-items-center gap-2">
                <span class="rounded-circle border shadow-sm" :style="{ backgroundColor: color.code || '#ccc', width: '26px', height: '26px' }"></span>
                <span class="font-monospace text-muted fs-8">{{ color.code || 'N/A' }}</span>
              </div>
            </td>
            <td class="fs-8 text-muted">{{ formatDate(color.created_at) }}</td>
            <td class="text-end">
              <div class="d-inline-flex gap-1">
                <button type="button" class="btn btn-sm btn-icon btn-label-primary" @click="openModal(color)" title="Edit">
                  <i class="fas fa-pen-to-square fs-8"></i>
                </button>
                <button type="button" class="btn btn-sm btn-icon btn-label-danger" @click="deleteColor(color.id)" title="Delete">
                  <i class="fas fa-trash-can fs-8"></i>
                </button>
              </div>
            </td>
          </tr>
          <tr v-else>
            <td colspan="4" class="text-center py-5 text-muted">
              <i class="fas fa-palette fa-3x mb-3 opacity-50"></i>
              <h6>No color swatches found</h6>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="colorModal" tabindex="-1">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-vuexy-lg rounded-4">
          <div class="modal-header border-bottom p-4">
            <h5 class="modal-title fw-bold text-heading">{{ editingId ? 'Edit Color' : 'Add New Color' }}</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body p-4">
            <form @submit.prevent="saveColor">
              <div class="mb-3">
                <label class="form-label fs-7 fw-semibold">Color Name (e.g. Royal Blue, Crimson)</label>
                <input type="text" class="form-control" required v-model="form.name" />
              </div>
              <div class="mb-3">
                <label class="form-label fs-7 fw-semibold">Hex Code</label>
                <div class="input-group">
                  <input type="color" class="form-control form-control-color" v-model="form.code" title="Choose color" />
                  <input type="text" class="form-control" v-model="form.code" placeholder="#7367f0" />
                </div>
              </div>
              <div class="d-flex justify-content-end gap-2 mt-4">
                <button type="button" class="btn btn-label-secondary rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold shadow-primary" :disabled="saving">
                  {{ saving ? 'Saving...' : 'Save Color' }}
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { formatDate } from '../utils/formatDate'

export default {
  name: 'Colors',
  data() {
    return {
      colorsList: [],
      colorsCount: 0,
      currentPage: 1,
      lastPage: 1,
      perPage: 10,
      filters: { search: '' },
      editingId: null,
      form: { name: '', code: '#7367f0' },
      saving: false,
      searchTimeout: null,
      sortField: 'name',
      sortOrder: 'asc'
    }
  },
  watch: {
    'filters.search'() {
      if (this.searchTimeout) clearTimeout(this.searchTimeout)
      this.searchTimeout = setTimeout(() => this.fetchColors(1), 300)
    }
  },
  mounted() {
    this.fetchColors(1)
  },
  methods: {
    formatDate,
    sortByField(field) {
      if (this.sortField === field) {
        this.sortOrder = this.sortOrder === 'asc' ? 'desc' : 'asc'
      } else {
        this.sortField = field
        this.sortOrder = 'asc'
      }
      this.fetchColors(1)
    },
    async fetchColors(page = 1) {
      try {
        const response = await this.$axios.get(`/api/colors?page=${page}&search=${this.filters.search}`)
        if (response.data.success) {
          const resData = response.data.data
          this.colorsList = resData.data || resData || []
          this.colorsCount = resData.total || this.colorsList.length
        }
      } catch (err) {
        console.error('Fetch colors error:', err)
      }
    },
    openModal(color = null) {
      if (color) {
        this.editingId = color.id
        this.form.name = color.name
        this.form.code = color.code || '#7367f0'
      } else {
        this.editingId = null
        this.form.name = ''
        this.form.code = '#7367f0'
      }
      const modalEl = document.getElementById('colorModal')
      if (window.bootstrap) {
        const bsModal = window.bootstrap.Modal.getOrCreateInstance(modalEl)
        bsModal.show()
      }
    },
    async saveColor() {
      if (!this.form.name.trim()) return
      this.saving = true
      try {
        let response
        if (this.editingId) {
          response = await this.$axios.put(`/api/colors/${this.editingId}`, this.form)
        } else {
          response = await this.$axios.post('/api/colors', this.form)
        }
        if (response.data.success) {
          const modalEl = document.getElementById('colorModal')
          if (window.bootstrap) {
            const bsModal = window.bootstrap.Modal.getInstance(modalEl)
            if (bsModal) bsModal.hide()
          }
          await this.fetchColors(1)
        }
      } catch (err) {
        console.error('Save color error:', err)
      } finally {
        this.saving = false
      }
    },
    async deleteColor(id) {
      if (!confirm('Are you sure you want to delete this color swatch?')) return
      try {
        const response = await this.$axios.delete(`/api/colors/${id}`)
        if (response.data.success) {
          await this.fetchColors(this.currentPage)
        }
      } catch (err) {
        console.error('Delete color error:', err)
      }
    }
  }
}
</script>
