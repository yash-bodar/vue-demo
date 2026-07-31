<template>
  <div class="card card-vuexy p-4">
    <!-- Header -->
    <div
      class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-4 pb-3 border-bottom"
    >
      <div class="d-flex align-items-center gap-3">
        <div class="badge bg-label-info rounded-3 p-3">
          <i class="fas fa-ruler-combined fs-3"></i>
        </div>
        <div>
          <h4 class="mb-0 fw-bold text-heading">Sizes Directory</h4>
          <small class="text-muted">Total {{ sizesCount }} size options</small>
        </div>
      </div>

      <button
        type="button"
        class="btn btn-primary rounded-pill px-4 shadow-primary"
        @click="openModal()"
      >
        <i class="fas fa-plus me-1"></i>Add Size
      </button>
    </div>

    <!-- Filter Bar -->
    <div class="row g-3 mb-4">
      <div class="col-12 col-md-6">
        <div class="input-group">
          <span class="input-group-text bg-transparent border-end-0 text-muted"
            ><i class="fas fa-search"></i
          ></span>
          <input
            type="search"
            class="form-control border-start-0 ps-0"
            v-model="filters.search"
            placeholder="Search size name..."
          />
        </div>
      </div>
    </div>

    <!-- Table -->
    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0">
        <thead>
          <tr>
            <th @click="sortByField('name')" class="cursor-pointer">Size Name</th>
            <th @click="sortByField('created_at')" class="cursor-pointer">Created Date</th>
            <th class="text-end">Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr v-if="sizesList.length > 0" v-for="size in sizesList" :key="size.id">
            <td>
              <span class="badge bg-label-primary fs-7 fw-bold">{{ size.name }}</span>
            </td>
            <td class="fs-8 text-muted">{{ formatDate(size.created_at) }}</td>
            <td class="text-end">
              <div class="d-inline-flex gap-1">
                <button
                  type="button"
                  class="btn btn-sm btn-icon btn-label-primary"
                  @click="openModal(size)"
                  title="Edit"
                >
                  <i class="fas fa-pen-to-square fs-8"></i>
                </button>
                <button
                  type="button"
                  class="btn btn-sm btn-icon btn-label-danger"
                  @click="deleteSize(size.id)"
                  title="Delete"
                >
                  <i class="fas fa-trash-can fs-8"></i>
                </button>
              </div>
            </td>
          </tr>
          <tr v-else>
            <td colspan="3" class="text-center py-5 text-muted">
              <i class="fas fa-ruler-combined fa-3x mb-3 opacity-50"></i>
              <h6>No size options found</h6>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Vue Reactive Modal Popup -->
    <div
      v-if="showModal"
      class="modal fade show d-block modal-backdrop-dark"
      tabindex="-1"
      style="z-index: 1060"
    >
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-vuexy-lg rounded-4">
          <div class="modal-header border-bottom p-4">
            <h5 class="modal-title fw-bold text-heading">
              {{ editingId ? 'Edit Size' : 'Add New Size' }}
            </h5>
            <button type="button" class="btn-close" @click="closeModal"></button>
          </div>
          <div class="modal-body p-4">
            <form @submit.prevent="saveSize">
              <div class="mb-3">
                <label class="form-label fs-7 fw-semibold">Size Label (e.g. S, M, L, XL, 42)</label>
                <input type="text" class="form-control" required v-model="form.name" />
              </div>
              <div class="d-flex justify-content-end gap-2 mt-4">
                <button
                  type="button"
                  class="btn btn-label-secondary rounded-pill px-4"
                  @click="closeModal"
                >
                  Cancel
                </button>
                <button
                  type="submit"
                  class="btn btn-primary rounded-pill px-4 fw-bold shadow-primary"
                  :disabled="saving"
                >
                  {{ saving ? 'Saving...' : 'Save Option' }}
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
  name: 'Sizes',
  data() {
    return {
      sizesList: [],
      sizesCount: 0,
      currentPage: 1,
      lastPage: 1,
      perPage: 10,
      filters: { search: '' },
      editingId: null,
      showModal: false,
      form: { name: '' },
      saving: false,
      searchTimeout: null,
      sortField: 'name',
      sortOrder: 'asc',
    }
  },
  watch: {
    'filters.search'() {
      if (this.searchTimeout) clearTimeout(this.searchTimeout)
      this.searchTimeout = setTimeout(() => this.fetchSizes(1), 300)
    },
  },
  mounted() {
    this.fetchSizes(1)
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
      this.fetchSizes(1)
    },
    async fetchSizes(page = 1) {
      try {
        const response = await this.$axios.get(
          `/api/sizes?page=${page}&search=${this.filters.search}`
        )
        if (response.data.success) {
          const resData = response.data.data
          this.sizesList = resData.data || resData || []
          this.sizesCount = resData.total || this.sizesList.length
        }
      } catch (err) {
        console.error('Fetch sizes error:', err)
      }
    },
    openModal(size = null) {
      if (size) {
        this.editingId = size.id
        this.form.name = size.name
      } else {
        this.editingId = null
        this.form.name = ''
      }
      this.showModal = true
    },
    closeModal() {
      this.showModal = false
      this.editingId = null
      this.form.name = ''
    },
    async saveSize() {
      if (!this.form.name.trim()) return
      this.saving = true
      try {
        let response
        if (this.editingId) {
          response = await this.$axios.put(`/api/sizes/${this.editingId}`, this.form)
        } else {
          response = await this.$axios.post('/api/sizes', this.form)
        }
        if (response.data.success) {
          this.closeModal()
          await this.fetchSizes(1)
        }
      } catch (err) {
        console.error('Save size error:', err)
      } finally {
        this.saving = false
      }
    },
    async deleteSize(id) {
      if (!confirm('Are you sure you want to delete this size option?')) return
      try {
        const response = await this.$axios.delete(`/api/sizes/${id}`)
        if (response.data.success) {
          await this.fetchSizes(this.currentPage)
        }
      } catch (err) {
        console.error('Delete size error:', err)
      }
    },
  },
}
</script>
