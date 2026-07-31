<template>
  <div class="card card-vuexy p-4">
    <div class="d-flex align-items-center justify-content-between pb-3 mb-4 border-bottom">
      <div class="d-flex align-items-center gap-3">
        <div class="badge bg-label-primary rounded-3 p-3">
          <i class="fas fa-folder-tree fs-3"></i>
        </div>
        <div>
          <h4 class="mb-0 fw-bold text-heading">
            {{ isEdit ? 'Edit Category' : 'Create New Category' }}
          </h4>
          <small class="text-muted">Manage store category information and status</small>
        </div>
      </div>
      <router-link to="/categories" class="btn btn-label-secondary rounded-pill px-4">
        <i class="fas fa-arrow-left me-1"></i>Back to Categories
      </router-link>
    </div>

    <form @submit.prevent="submitForm">
      <div class="row g-4">
        <!-- Main Details -->
        <div class="col-lg-8">
          <div class="mb-4">
            <label class="form-label fs-7 fw-bold text-uppercase text-muted mb-2"
              >Category Name</label
            >
            <div class="input-group">
              <span class="input-group-text bg-transparent text-muted"
                ><i class="fas fa-tag fs-7"></i
              ></span>
              <input
                v-model="form.name"
                class="form-control"
                placeholder="e.g. Electronics, Footwear"
                required
              />
            </div>
          </div>
        </div>

        <!-- Sidebar / Settings -->
        <div class="col-lg-4">
          <div class="p-3 bg-light rounded-4 border">
            <h6 class="fw-bold text-heading mb-3">
              <i class="fas fa-sliders me-2 text-primary"></i>Status & Actions
            </h6>

            <div class="mb-4">
              <label class="form-label fs-7 fw-bold text-uppercase text-muted mb-2"
                >Visibility Status</label
              >
              <select v-model="form.status" class="form-select" required>
                <option value="Active">Active</option>
                <option value="Inactive">Inactive</option>
              </select>
            </div>

            <button
              type="submit"
              class="btn btn-primary w-100 rounded-pill py-2.5 fw-bold shadow-primary"
            >
              <i class="fas fa-check me-2"></i>{{ isEdit ? 'Save Changes' : 'Create Category' }}
            </button>
          </div>
        </div>
      </div>
    </form>
  </div>
</template>

<script>
export default {
  data() {
    return {
      form: {
        id: null,
        name: '',
        status: 'Active',
      },
      isEdit: false,
    }
  },
  mounted() {
    const id = this.$route.params.id
    if (id) {
      this.isEdit = true
      this.$axios.get(`/api/categories/${id}`).then((res) => {
        this.form = res.data.data
      })
    }
  },
  methods: {
    submitForm() {
      let formData = new FormData()
      formData.append('name', this.form.name)
      formData.append('status', this.form.status)
      if (this.isEdit) {
        formData.append('id', this.form.id)
      }

      this.$axios.post('/api/categories', formData).then(() => this.$router.push('/categories'))
    },
  },
}
</script>
