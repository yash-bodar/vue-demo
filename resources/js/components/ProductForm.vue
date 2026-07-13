<template>
  <div class="card bg-white shadow-sm border-0">
    <div class="card-header bg-gradient-primary text-light bg-primary-linear py-3 px-4 border-0">
      <div class="row align-items-center">
        <div class="col">
          <h4 class="mb-0 fw-bold d-flex align-items-center">
            <i class="bi bi-box-seam me-2"></i>
            {{ isEdit ? 'Edit Product' : 'Create Product' }}
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
                <i class="fas fa-tag me-2 text-primary"></i> Product Name
              </label>
              <input v-model="form.name" class="form-control form-control-lg" placeholder="Enter product name" required>
            </div>
            
            <div class="mb-4">
              <label class="form-label fw-semibold">
                <i class="fas fa-align-left me-2 text-primary"></i> Description
              </label>
              <textarea v-model="form.description" class="form-control" rows="4" placeholder="Describe your product..." required></textarea>
            </div>
            
            <div class="row">
              <div class="col-md-6 mb-4">
                <label class="form-label fw-semibold">
                  <i class="fas fa-dollar-sign me-2 text-primary"></i> Price
                </label>
                <div class="input-group">
                  <span class="input-group-text">{{ form.currency }}</span>
                  <input v-model="form.price" class="form-control" type="number" step="0.01" min="0" placeholder="0.00" required>
                </div>
              </div>
              
              <div class="col-md-6 mb-4">
                <label class="form-label fw-semibold">
                  <i class="fas fa-boxes me-2 text-primary"></i> Quantity Available
                </label>
                <input v-model="form.stock" class="form-control" type="number" step="1" min="0" placeholder="0" required>
              </div>
            </div>
            
            <div class="mb-4">
              <label class="form-label fw-semibold">
                <i class="fas fa-image me-2 text-primary"></i> Product Image
              </label>
              <div class="border rounded p-3 bg-light">
                <input class="form-control" type="file" @change="handleFileUpload" :required="!isEdit" accept="image/*" />
                <div v-if="form.image && typeof form.image === 'string'" class="mt-3">
                  <small class="text-muted">Current image:</small>
                  <img :src="$getImageUrl(form.image)" class="img-thumbnail mt-2" style="max-height: 100px;">
                </div>
              </div>
            </div>
          </div>
          
          <!-- Right Column -->
          <div class="col-lg-4">
            <div class="card bg-light border-0">
              <div class="card-body">
                <h6 class="card-title fw-bold mb-4">
                  <i class="fas fa-cog me-1 text-primary"></i> Product Settings
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
                
                <div class="mb-4">
                  <label class="form-label fw-semibold">
                    <i class="fas fa-money-bill-wave me-2 text-primary"></i> Currency
                  </label>
                  <select v-model="form.currency" class="form-select" required>
                    <option value="USD">USD - US Dollar ($)</option>
                    <option value="EUR">EUR - Euro (€)</option>
                    <option value="GBP">GBP - British Pound (£)</option>
                    <option value="CAD">CAD - Canadian Dollar (C$)</option>
                    <option value="AUD">AUD - Australian Dollar (A$)</option>
                    <option value="INR">INR - Indian Rupee (₹)</option>
                    <option value="AED">AED - UAE Dirham (د.إ)</option>
                  </select>
                </div>
                
                <div class="mb-4">
                  <label class="form-label fw-semibold">
                    <i class="fas fa-folder me-2 text-primary"></i> Category
                  </label>
                  <select v-model="form.category_id" class="form-select" required>
                    <option value="" disabled>Select a category</option>
                    <option v-for="category in categories" :key="category.id" :value="category.id" :selected="category.id === form.category_id">
                      {{ category.name }}
                    </option>
                  </select>
                </div>
                
                <div class="d-grid gap-2">
                  <router-link class="btn btn-outline-secondary" to="/products">
                    <i class="fas fa-arrow-left me-1"></i> Back to Products
                  </router-link>
                  <button class="btn btn-primary btn-lg bg-primary-linear" type="submit">
                    <i class="fas fa-check me-1"></i> {{ isEdit ? 'Update Product' : 'Create Product' }}
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
                price: 0,
                stock: 0,
                description: '',
                image: null,
                status: 'Active',
                currency: 'USD',
                category_id: ''
            },
            isEdit : false,
            categories: []
        }
    },
    mounted(){
      const id = this.$route.params.id;
      if(id) {
        this.isEdit = true
        this.$axios.get(`/api/products/${id}`).then(res => {
          this.form = res.data.data
          this.form.image = null
        })
      }
      this.getCategories();
    },
    methods: {
        submitForm() {
            let formData = new FormData()
            formData.append('name', this.form.name)
            formData.append('price', this.form.price)
            formData.append('stock', this.form.stock)
            formData.append('description', this.form.description)
            if (this.isEdit) {
                formData.append('id', this.form.id)
            }
            if (this.form.image) {
                formData.append('image', this.form.image)
            }
            formData.append('status', this.form.status)
            formData.append('currency', this.form.currency)
            formData.append('category_id', this.form.category_id)

            this.$axios.post('/api/products', formData).then(
                () => this.$router.push('/products')
            )
        },
        handleFileUpload(event) {
            this.form.image = event.target.files[0]
        },
        async getCategories() {
          const response = await this.$axios.get('/api/get-categories');
          const data = response.data;
          if(data.success) {
            this.categories = data.data;
          }
        },
    }
}

</script>