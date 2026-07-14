<template>
  <div class="card bg-white shadow-sm border-0">
    <div class="card-header bg-gradient-primary text-light bg-primary-linear py-3 px-4 border-0">
      <div class="row align-items-center">
        <div class="col">
          <h4 class="mb-0 fw-bold d-flex align-items-center">
            <i class="fas fa-ticket-alt me-2"></i>
            {{ isEdit ? 'Edit Coupon' : 'Create Coupon' }}
          </h4>
        </div>
      </div>
    </div>
    <div class="card-body p-4">
      <form @submit.prevent="submitForm">
        <div class="row g-4">
          <!-- Left Column -->
          <div class="col-lg-8">
            <!-- Coupon Code -->
            <div class="mb-4">
              <label class="form-label fw-semibold">
                <i class="fas fa-barcode me-2 text-primary"></i> Coupon Code
              </label>
              <input 
                v-model="form.code" 
                class="form-control form-control-lg" 
                placeholder="e.g., SAVE20" 
                @input="form.code = form.code.toUpperCase()"
                :readonly="isEdit"
                required
              >
              <small class="text-muted">Unique code customers will use</small>
            </div>

            <!-- Description -->
            <div class="mb-4">
              <label class="form-label fw-semibold">
                <i class="fas fa-align-left me-2 text-primary"></i> Description
              </label>
              <textarea 
                v-model="form.description" 
                class="form-control" 
                rows="3" 
                placeholder="e.g., 20% off on all products"
              ></textarea>
            </div>

            <!-- Discount Settings -->
            <div class="row">
              <div class="col-md-6 mb-4">
                <label class="form-label fw-semibold">
                  <i class="fas fa-percent me-2 text-primary"></i> Discount Type
                </label>
                <select v-model="form.discount_type" class="form-select form-select-lg" required>
                  <option value="percentage">Percentage (%)</option>
                  <option value="fixed">Fixed Amount ($)</option>
                </select>
              </div>

              <div class="col-md-6 mb-4">
                <label class="form-label fw-semibold">
                  <i class="fas fa-dollar-sign me-2 text-primary"></i> Discount Value
                </label>
                <div class="input-group input-group-lg">
                  <input 
                    v-model.number="form.discount_value" 
                    class="form-control" 
                    type="number" 
                    step="0.01" 
                    min="0" 
                    placeholder="0"
                    required
                  >
                  <span class="input-group-text">
                    {{ form.discount_type === 'percentage' ? '%' : '$' }}
                  </span>
                </div>
              </div>
            </div>

            <!-- Minimum Purchase -->
            <div class="mb-4">
              <label class="form-label fw-semibold">
                <i class="fas fa-shopping-cart me-2 text-primary"></i> Minimum Purchase Amount
              </label>
              <div class="input-group">
                <span class="input-group-text">$</span>
                <input 
                  v-model.number="form.min_purchase_amount" 
                  class="form-control" 
                  type="number" 
                  step="0.01" 
                  min="0"
                  placeholder="Leave empty for no minimum"
                >
              </div>
              <small class="text-muted">Minimum amount customer must spend to use this coupon</small>
            </div>

            <!-- Usage Limits -->
            <div class="row">
              <div class="col-md-6 mb-4">
                <label class="form-label fw-semibold">
                  <i class="fas fa-repeat me-2 text-primary"></i> Max Total Uses
                </label>
                <input 
                  v-model.number="form.max_uses" 
                  class="form-control" 
                  type="number" 
                  min="1"
                  placeholder="Leave empty for unlimited"
                >
                <small class="text-muted">Total times this coupon can be used</small>
              </div>

              <div class="col-md-6 mb-4">
                <label class="form-label fw-semibold">
                  <i class="fas fa-user-check me-2 text-primary"></i> Max Uses Per User
                </label>
                <input 
                  v-model.number="form.max_uses_per_user" 
                  class="form-control" 
                  type="number" 
                  min="1"
                  placeholder="Leave empty for unlimited"
                >
                <small class="text-muted">Times a single user can use this coupon</small>
              </div>
            </div>

            <!-- Validity Period -->
            <div class="row">
              <div class="col-md-6 mb-4">
                <label class="form-label fw-semibold">
                  <i class="fas fa-calendar-check me-2 text-primary"></i> Valid From
                </label>
                <input 
                  v-model="form.valid_from" 
                  class="form-control" 
                  type="datetime-local"
                >
                <small class="text-muted">Leave empty to start immediately</small>
              </div>

              <div class="col-md-6 mb-4">
                <label class="form-label fw-semibold">
                  <i class="fas fa-calendar-times me-2 text-primary"></i> Valid Until
                </label>
                <input 
                  v-model="form.valid_until" 
                  class="form-control" 
                  type="datetime-local"
                >
                <small class="text-muted">Leave empty for no expiry date</small>
              </div>
            </div>
          </div>

          <!-- Right Column -->
          <div class="col-lg-4">
            <div class="card bg-light border-0">
              <div class="card-body">
                <h6 class="card-title fw-bold mb-4">
                  <i class="fas fa-cog me-1 text-primary"></i> Coupon Settings
                </h6>

                <!-- Status -->
                <div class="mb-4">
                  <label class="form-label fw-semibold">
                    <i class="fas fa-toggle-on me-2 text-primary"></i> Status
                  </label>
                  <select v-model="form.is_active" class="form-select" required>
                    <option :value="true">Active</option>
                    <option :value="false">Inactive</option>
                  </select>
                </div>

                <!-- Summary -->
                <div class="alert alert-info mb-4">
                  <h6 class="alert-heading mb-2">
                    <i class="fas fa-info-circle me-1"></i> Summary
                  </h6>
                  <ul class="list-unstyled mb-0 small">
                    <li>
                      <strong>Type:</strong> 
                      {{ form.discount_type === 'percentage' ? 'Percentage' : 'Fixed Amount' }}
                    </li>
                    <li class="mt-1">
                      <strong>Discount:</strong> 
                      <span class="text-success fw-bold">
                        <span v-if="form.discount_type === 'percentage'">{{ form.discount_value }}%</span>
                        <span v-else>${{ form.discount_value }}</span>
                      </span>
                    </li>
                    <li v-if="form.min_purchase_amount" class="mt-1">
                      <strong>Min. Purchase:</strong> ${{ form.min_purchase_amount }}
                    </li>
                    <li class="mt-1">
                      <strong>Max Uses:</strong> {{ form.max_uses ? form.max_uses : '∞' }}
                    </li>
                    <li v-if="form.max_uses_per_user" class="mt-1">
                      <strong>Per User:</strong> {{ form.max_uses_per_user }}
                    </li>
                  </ul>
                </div>

                <!-- Usage Stats (for edit mode) -->
                <div v-if="isEdit" class="alert alert-secondary">
                  <h6 class="alert-heading mb-2">
                    <i class="fas fa-chart-bar me-1"></i> Usage Stats
                  </h6>
                  <p class="mb-0 small">
                    <strong>Used:</strong> {{ originalData.times_used }} times
                  </p>
                </div>

                <!-- Form Actions -->
                <div class="d-grid gap-2">
                  <button 
                    type="submit" 
                    class="btn btn-primary btn-lg fw-semibold"
                    :disabled="loading"
                  >
                    <span v-if="!loading">
                      <i class="fas fa-save me-1"></i>{{ isEdit ? 'Update Coupon' : 'Create Coupon' }}
                    </span>
                    <span v-else>
                      <i class="fas fa-spinner fa-spin me-1"></i>Saving...
                    </span>
                  </button>
                  <router-link to="/coupons" class="btn btn-outline-secondary btn-lg fw-semibold">
                    <i class="fas fa-arrow-left me-1"></i>Cancel
                  </router-link>
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
import axios from 'axios'

export default {
  name: 'CouponForm',
  data() {
    return {
      form: {
        code: '',
        description: '',
        discount_type: 'percentage',
        discount_value: 0,
        min_purchase_amount: null,
        max_uses: null,
        max_uses_per_user: null,
        valid_from: null,
        valid_until: null,
        is_active: true,
      },
      originalData: {},
      loading: false,
      isEdit: false,
    }
  },
  computed: {
    couponId() {
      return this.$route.params.id
    },
  },
  mounted() {
    if (this.couponId) {
      this.isEdit = true
      this.fetchCouponData()
    }
  },
  methods: {
    async fetchCouponData() {
      try {
        const response = await axios.get(`/api/coupons/${this.couponId}`)
        if (response.data.success) {
          const coupon = response.data.data
          this.originalData = { ...coupon }
          this.form = {
            code: coupon.code,
            description: coupon.description,
            discount_type: coupon.discount_type,
            discount_value: coupon.discount_value,
            min_purchase_amount: coupon.min_purchase_amount,
            max_uses: coupon.max_uses,
            max_uses_per_user: coupon.max_uses_per_user,
            valid_from: coupon.valid_from ? coupon.valid_from.slice(0, 16) : null,
            valid_until: coupon.valid_until ? coupon.valid_until.slice(0, 16) : null,
            is_active: coupon.is_active,
          }
        }
      } catch (error) {
        alert('Failed to load coupon data')
        this.$router.push('/coupons')
      }
    },
    async submitForm() {
      this.loading = true
      try {
        const formData = new FormData()

        Object.keys(this.form).forEach((key) => {
        const value = this.form[key]

        if (value !== null && value !== undefined && value !== '') {
            formData.append(key, value)
        }
        })
        console.log('formData', formData)
        // Convert boolean properly
        formData.set('is_active', this.form.is_active ? 1 : 0)
        console.log('formData', formData)

        let response
        if (this.isEdit) {
            formData.append('_method', 'PUT') // if Laravel
            response = await axios.post(`/api/coupons/${this.couponId}`, formData)
        } else {
            response = await axios.post('/api/coupons', formData)
        }

        if (response.data.success) {
          alert(response.data.message)
          this.$router.push('/coupons')
        }
      } catch (error) {
        const message = error.response?.data?.message || error.response?.data?.errors?.[Object.keys(error.response?.data?.errors)[0]][0] || 'Failed to save coupon'
        alert(message)
      } finally {
        this.loading = false
      }
    },
  },
}
</script>