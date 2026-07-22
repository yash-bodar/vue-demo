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
            
            <!-- Variants Section -->
            <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden bg-white">
              <div class="card-header bg-gradient-primary text-light bg-primary-linear py-3 px-4 d-flex justify-content-between align-items-center border-0">
                <div>
                  <h6 class="fw-bold mb-0 text-white">
                    <i class="fas fa-layer-group me-2"></i>Product Variants
                  </h6>
                  <small class="text-white-50">Manage size, color, SKU and stock variations</small>
                </div>
                <button type="button" class="btn btn-light btn-sm rounded-pill px-3 shadow-sm text-primary fw-semibold" @click="addVariant">
                  <i class="fas fa-plus me-1"></i> Add Variant
                </button>
              </div>

              <div class="card-body p-4">
                <div v-if="form.variants && form.variants.length === 0" class="text-center py-4 bg-light rounded-3 border border-dashed">
                  <i class="fas fa-tags text-muted fa-2x mb-2 opacity-50"></i>
                  <p class="mb-0 text-muted small fw-semibold">No variants added yet.</p>
                  <small class="text-muted d-block mt-1">This product will sell as a single item using the base price & quantity above.</small>
                </div>

                <div v-else class="d-flex flex-column gap-3">
                  <div v-for="(variant, index) in form.variants" :key="index" class="p-3 bg-light rounded-4 border position-relative transition-all">
                    <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                      <div class="d-flex align-items-center gap-2">
                        <span class="badge bg-primary text-white px-3 py-2 rounded-pill fw-bold" style="font-size: 0.78rem;">
                          Variant Option #{{ index + 1 }}
                        </span>
                        <span v-if="variant.sku" class="badge bg-white text-dark border rounded-pill px-2 py-1 small">
                          SKU: {{ variant.sku }}
                        </span>
                      </div>
                      <button type="button" class="btn btn-link text-danger p-0 border-0 fs-5 shadow-none" title="Delete Variant" @click="removeVariant(index)">
                        <i class="fas fa-trash-alt"></i>
                      </button>
                    </div>

                    <div class="row g-3 align-items-end">
                      <div class="col-md-3 col-6">
                        <label class="form-label small fw-bold text-dark mb-1">Size</label>
                        <select v-model="variant.size_id" class="form-select variant-input-field rounded-3 shadow-none border" @change="updateVariantName(variant)">
                          <option value="">No Size</option>
                          <option v-for="size in sizes" :key="size.id" :value="size.id">{{ size.name }}</option>
                        </select>
                      </div>

                      <div class="col-md-3 col-6">
                        <label class="form-label small fw-bold text-dark mb-1">Color</label>
                        <select v-model="variant.color_id" class="form-select variant-input-field rounded-3 shadow-none border" @change="updateVariantName(variant)">
                          <option value="">No Color</option>
                          <option v-for="color in colors" :key="color.id" :value="color.id">
                            {{ color.name }}
                          </option>
                        </select>
                      </div>

                      <div class="col-md-6 col-12">
                        <label class="form-label small fw-bold text-dark mb-1">Variant Name</label>
                        <div class="input-group">
                          <span class="input-group-text bg-white text-muted border-end-0 rounded-start-3"><i class="fas fa-tag"></i></span>
                          <input v-model="variant.name" class="form-control variant-input-field rounded-end-3" placeholder="e.g. Black / XL" required>
                        </div>
                      </div>

                      <div class="col-md-4 col-12">
                        <label class="form-label small fw-bold text-dark mb-1">SKU Code</label>
                        <div class="input-group">
                          <span class="input-group-text bg-white text-muted border-end-0 rounded-start-3"><i class="fas fa-barcode"></i></span>
                          <input v-model="variant.sku" class="form-control variant-input-field rounded-end-3" placeholder="Unique SKU">
                        </div>
                      </div>

                      <div class="col-md-4 col-6">
                        <label class="form-label small fw-bold text-dark mb-1">Price Override</label>
                        <div class="input-group">
                          <span class="input-group-text bg-white text-primary fw-bold border-end-0 rounded-start-3">{{ form.currency }}</span>
                          <input v-model="variant.price" class="form-control variant-input-field rounded-end-3" type="number" step="0.01" min="0" placeholder="Base Price">
                        </div>
                      </div>

                      <div class="col-md-4 col-6">
                        <label class="form-label small fw-bold text-dark mb-1">Stock Quantity</label>
                        <div class="input-group">
                          <span class="input-group-text bg-white text-muted border-end-0 rounded-start-3"><i class="fas fa-boxes"></i></span>
                          <input v-model.number="variant.stock" class="form-control variant-input-field rounded-end-3 fw-bold text-primary" type="number" min="0" required>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <div class="mb-4">
              <label class="form-label fw-semibold">
                <i class="fas fa-image me-2 text-primary"></i> Product Image
              </label>
              <div class="border rounded p-3 bg-light">
                <input class="form-control" type="file" @change="handleFileUpload" :required="!isEdit" accept="image/*" />
                <div v-if="existingImage || (form.image && typeof form.image === 'string')" class="mt-3">
                  <small class="text-muted d-block fw-semibold mb-1">Current Image Preview:</small>
                  <img :src="getImageUrl(existingImage || form.image)" class="img-thumbnail mt-1 shadow-sm rounded-3" style="max-height: 120px; object-fit: cover;">
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
import { getImageUrl } from '../utils/ImageUrl'

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
                category_id: '',
                variants: []
            },
            existingImage: null,
            isEdit : false,
            categories: [],
            sizes: [],
            colors: []
        }
    },
    mounted(){
      const id = this.$route.params.id;
      if(id) {
        this.isEdit = true
        this.$axios.get(`/api/products/${id}`).then(res => {
          const productData = res.data.data;
          this.existingImage = productData.image;
          this.form = productData;
          this.form.image = null;
          if (!this.form.variants) {
            this.form.variants = [];
          }
        })
      }
      this.getCategories();
      this.getSizes();
      this.getColors();
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
            formData.append('variants', JSON.stringify(this.form.variants))

            this.$axios.post('/api/products', formData).then(
                () => this.$router.push('/products')
            )
        },
        handleFileUpload(event) {
            this.form.image = event.target.files[0]
        },
        addVariant() {
            this.form.variants.push({
                id: null,
                size_id: '',
                color_id: '',
                name: '',
                price: '',
                stock: 0,
                sku: ''
            });
        },
        removeVariant(index) {
            this.form.variants.splice(index, 1);
        },
        updateVariantName(variant) {
          const sizeName = this.sizes.find(s => s.id == variant.size_id)?.name || '';
          const colorName = this.colors.find(c => c.id == variant.color_id)?.name || '';
          if (colorName && sizeName) {
            variant.name = `${colorName} / ${sizeName}`;
          } else if (colorName) {
            variant.name = colorName;
          } else if (sizeName) {
            variant.name = sizeName;
          }
        },
        async getCategories() {
          const response = await this.$axios.get('/api/get-categories');
          const data = response.data;
          if(data.success) {
            this.categories = data.data;
          }
        },
        async getSizes() {
          const response = await this.$axios.get('/api/get-sizes');
          const data = response.data;
          if(data.success) {
            this.sizes = data.data;
          }
        },
        async getColors() {
          const response = await this.$axios.get('/api/get-colors');
          const data = response.data;
          if(data.success) {
            this.colors = data.data;
          }
        },
        getImageUrl
    }
}
</script>