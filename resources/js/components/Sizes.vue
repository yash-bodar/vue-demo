<template>
  <div>
    <!-- Header/Filter Bar -->
    <div class="card-header bg-gradient-primary text-light py-3 px-4 border-0 bg-primary-linear">
      <div class="row align-items-center filter-header">
        <div class="col-12 col-md-auto mt-1">
          <h5 class="mb-0 fw-bold d-flex align-items-center">
            <i class="fas fa-ruler me-2"></i>Sizes
            <span class="badge bg-white color-primary ms-2 px-2 py-1 small fw-semibold">{{ sizesCount }}</span>
          </h5>
        </div>
        <div class="col-12 col-md-auto mt-1">
          <div class="search-input-wrapper">
            <i class="fas fa-search search-icon"></i>
            <input type="search" class="form-control p-1 ps-5" v-model="filters.search" placeholder="Search sizes...">
          </div>
        </div>
        <div class="col-12 col-md-auto ms-auto mt-1">
          <button class="btn btn-dark btn-sm p-2 shadow-sm" title="Add New Size" @click="openModal()"><i class="fa fa-plus"></i></button>
        </div>
      </div>
    </div>

    <!-- Sizes List -->
    <div class="card-body p-0">
      <div class="table-container overflow-y-auto">
        <table class="table table-hover align-middle mb-0">
          <thead class="bg-primary-linear text-light sticky-top">
            <tr>
              <th @click="sortByField('name')" class="cursor-pointer ps-4">Size Name <i :class="getSortIcon('name')" class="ms-1"></i></th>
              <th @click="sortByField('created_at')" class="cursor-pointer">Created At <i :class="getSortIcon('created_at')" class="ms-1"></i></th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
            <tr v-if="sizesList.length > 0" v-for="size in sizesList" :key="size.id" class="hover-row">
              <td class="ps-4">
                <div class="fw-semibold text-primary fs-6">{{ size.name }}</div>
                <small class="text-muted">ID: #{{ size.id }}</small>
              </td>
              <td>
                <div class="d-flex align-items-center gap-2">
                  <i class="fa fa-calendar text-muted"></i><span>{{ formatDate(size.created_at) }}</span>
                </div>
              </td>
              <td>
                <div class="btn-group gap-2">
                  <button class="btn btn-sm btn-outline-primary fw-semibold rounded-1 p-2" @click="openModal(size)" title="Edit">
                    <i class="fa fa-pencil"></i>
                  </button>
                  <button class="btn btn-sm btn-outline-danger fw-semibold rounded-1 p-2" type="button" @click="deleteSize(size.id)" title="Delete">
                    <i class="fa fa-trash"></i>
                  </button>
                </div>
              </td>
            </tr>
            <tr v-else>
              <td colspan="3" class="text-center py-5">
                <div class="text-muted">
                  <i class="fa fa-ruler fs-1 d-block mb-3"></i>
                  <h5>No sizes found</h5>
                  <p>Try adding a size or adjusting your search criteria.</p>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <div class="card-footer bg-white border-0 p-3" v-if="lastPage > 1">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 pagination-div">
          <span class="text-muted small">Showing {{ (currentPage - 1) * perPage + 1 }} to {{ Math.min(currentPage * perPage, sizesCount) }} of {{ sizesCount }}</span>
          <div class="d-flex gap-1">
            <button class="btn btn-sm btn-outline-secondary rounded-2" :disabled="currentPage === 1" @click="fetchSizes(currentPage - 1)"><i class="fa fa-chevron-left fa-xs"></i></button>
            <button v-for="page in lastPage" :key="page" :disabled="page === currentPage" class="btn opacity-100 rounded-2 btn-sm"
              :class="page === currentPage ? 'bg-primary btn-outline-primary text-white' : 'btn-outline-secondary'" @click="fetchSizes(page)">
              {{ page }}
            </button>
            <button class="btn btn-sm btn-outline-secondary rounded-2" :disabled="currentPage === lastPage" @click="fetchSizes(currentPage + 1)">
              <i class="fa fa-chevron-right fa-xs"></i>
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Size Modal -->
    <div class="modal fade" id="sizeModal" tabindex="-1" aria-labelledby="sizeModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content border-0 shadow-lg">
          <div class="modal-header border-light">
            <h5 class="modal-title fw-bold" id="sizeModalLabel">{{ editingId ? 'Edit Size' : 'Add Size' }}</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body p-4">
            <form @submit.prevent="saveSize">
              <div class="mb-3">
                <label for="sizeName" class="form-label fw-bold text-muted small">Size Name *</label>
                <input type="text" class="form-control" id="sizeName" required placeholder="e.g. S, M, L, XL, 38, 40" v-model="form.name">
              </div>
              <div class="d-flex justify-content-end gap-2 mt-4">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary" :disabled="submitting">
                  <span v-if="submitting" class="spinner-border spinner-border-sm me-2"></span>
                  <i class="fas fa-save me-2"></i>Save Size
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
import { showToast } from '../utils/ui-toasts'

export default {
  name: 'Sizes',
  data() {
    return {
      sizesList: [],
      sizesCount: 0,
      currentPage: 1,
      lastPage: 1,
      perPage: 10,
      submitting: false,
      editingId: null,
      filters: {
        sort_by: 'id',
        sort_order: 'desc',
        search: ''
      },
      form: {
        name: ''
      }
    }
  },
  watch: {
    'filters.search'() {
      this.fetchSizes(1);
    }
  },
  mounted() {
    this.fetchSizes();
  },
  methods: {
    formatDate,
    fetchSizes(page = 1) {
      const params = new URLSearchParams({
        page: page,
        ...this.filters
      });
      this.$axios.get(`/api/sizes?${params.toString()}`)
        .then(res => {
          this.sizesList = res.data.data.data;
          this.sizesCount = res.data.data.total;
          this.currentPage = res.data.data.current_page;
          this.lastPage = res.data.data.last_page;
          this.perPage = res.data.data.per_page;
        })
        .catch(err => {
          console.error('Failed to fetch sizes', err);
          showToast('Failed to fetch sizes', 'error');
        });
    },
    openModal(size = null) {
      if (size) {
        this.editingId = size.id;
        this.form.name = size.name;
      } else {
        this.editingId = null;
        this.form.name = '';
      }
      $('#sizeModal').modal('show');
    },
    saveSize() {
      this.submitting = true;
      const url = this.editingId ? `/api/sizes/${this.editingId}` : '/api/sizes';
      const method = this.editingId ? 'put' : 'post';
      
      this.$axios[method](url, this.form)
        .then(res => {
          if (res.data.success) {
            showToast(this.editingId ? 'Size updated successfully!' : 'Size created successfully!', 'success');
            $('#sizeModal').modal('hide');
            this.fetchSizes(this.currentPage);
          } else {
            showToast(res.data.message || 'Error occurred', 'error');
          }
        })
        .catch(err => {
          console.error('Error saving size', err);
          showToast(err.response?.data?.message || 'Error saving size', 'error');
        })
        .finally(() => {
          this.submitting = false;
        });
    },
    deleteSize(id) {
      if (!confirm('Are you sure you want to delete this size?')) return;
      this.$axios.delete(`/api/sizes/${id}`)
        .then(res => {
          if (res.data.success) {
            showToast('Size deleted successfully!', 'success');
            this.fetchSizes(this.currentPage);
          } else {
            showToast(res.data.message || 'Error occurred', 'error');
          }
        })
        .catch(err => {
          console.error('Error deleting size', err);
          showToast('Error deleting size', 'error');
        });
    },
    sortByField(field) {
      if (this.filters.sort_by === field) {
        this.filters.sort_order = this.filters.sort_order === 'asc' ? 'desc' : 'asc';
      } else {
        this.filters.sort_by = field;
        this.filters.sort_order = 'asc';
      }
      this.fetchSizes(1);
    },
    getSortIcon(field) {
      if (this.filters.sort_by !== field) return 'fas fa-sort text-muted';
      return this.filters.sort_order === 'asc' ? 'fas fa-sort-up text-primary' : 'fas fa-sort-down text-primary';
    }
  }
}
</script>
