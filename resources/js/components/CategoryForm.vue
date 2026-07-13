<template>
  <div class="card bg-white shadow-sm border-0">
    <div class="card-header bg-gradient-primary text-light bg-primary-linear py-3 px-4 border-0">
      <div class="row align-items-center">
        <div class="col">
          <h4 class="mb-0 fw-bold d-flex align-items-center">
            <i class="bi bi-folder me-2"></i>
            {{ isEdit ? 'Edit Category' : 'Create Category' }}
          </h4>
        </div>
      </div>
    </div>
    <div class="card-body p-4">
      <form @submit.prevent="submitForm">
        <div class="row g-4">
          <!-- Left Column -->
          <div class="col-lg-8">
            <div class="mb-4">
              <label class="form-label fw-semibold">
                <i class="fas fa-tag me-2 text-primary"></i> Category Name
              </label>
              <input v-model="form.name" class="form-control form-control-lg" placeholder="Enter category name" required>
            </div>
          </div>
          
          <!-- Right Column -->
          <div class="col-lg-4">
            <div class="card bg-light border-0">
              <div class="card-body">
                <h6 class="card-title fw-bold mb-4">
                  <i class="fas fa-cog me-1 text-primary"></i> Category Settings
                </h6>
                
                <div class="mb-4">
                  <label class="form-label fw-semibold">
                    <i class="fas fa-toggle-on me-2 text-primary"></i> Status
                  </label>
                  <select v-model="form.status" class="form-select" required>
                    <option value="Active">Active</option>
                    <option value="Inactive">Inactive</option>
                  </select>
                </div>
                <div class="d-grid gap-2">
                  <router-link class="btn btn-outline-secondary" to="/categories">
                    <i class="fas fa-arrow-left me-1"></i> Back to Categories
                  </router-link>
                  <button class="btn btn-primary btn-lg bg-primary-linear" type="submit">
                    <i class="fas fa-check me-1"></i> {{ isEdit ? 'Update Category' : 'Create Category' }}
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </form>
    </div>
  </div>
</template>

<script>

export default {
    data(){
        return {
            form: {
                id: null,
                name: '',
                status: 'Active',
            },
            isEdit : false,
        }
    },
    mounted(){
        const id = this.$route.params.id;
        if(id) {
            this.isEdit = true
            this.$axios.get(`/api/categories/${id}`).then(res => {
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

            this.$axios.post('/api/categories', formData).then(
                () => this.$router.push('/categories')
            )
        }
    }
}

</script>