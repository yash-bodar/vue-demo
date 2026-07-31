<template>
  <div class="coupon-section card bg-light p-3 mb-3">
    <h5 class="mb-3"><i class="fas fa-tag me-2"></i>Apply Coupon Code</h5>

    <div v-if="appliedCoupon" class="alert alert-success mb-3">
      <div class="d-flex justify-content-between align-items-center">
        <div>
          <strong>{{ appliedCoupon.code }}</strong>
          <p class="mb-0 small">{{ appliedCoupon.description }}</p>
          <p class="mb-0 small text-success fw-bold">
            Discount: {{ currency }}{{ appliedCoupon.discount_amount }}
          </p>
        </div>
        <button class="btn btn-sm btn-outline-danger" @click="removeCoupon">
          <i class="fas fa-times"></i> Remove
        </button>
      </div>
    </div>

    <div v-else class="input-group">
      <input
        v-model="couponCode"
        type="text"
        class="form-control"
        placeholder="Enter coupon code..."
        :disabled="loading"
        @keyup.enter="validateCoupon"
      />
      <button
        class="btn btn-primary"
        type="button"
        @click="validateCoupon"
        :disabled="loading || !couponCode.trim()"
      >
        <span v-if="!loading">Apply</span>
        <span v-else> <i class="fas fa-spinner fa-spin me-1"></i>Validating... </span>
      </button>
    </div>

    <div v-if="error" class="alert alert-danger mt-2 mb-0">
      {{ error }}
    </div>
  </div>
</template>

<script>
import axios from 'axios'

export default {
  name: 'CouponApplier',
  props: {
    cartTotal: {
      type: Number,
      required: true,
    },
    currency: {
      type: String,
      default: '$',
    },
  },
  data() {
    return {
      couponCode: '',
      appliedCoupon: null,
      loading: false,
      error: null,
    }
  },
  methods: {
    async validateCoupon() {
      if (!this.couponCode.trim()) {
        this.error = 'Please enter a coupon code'
        return
      }

      this.loading = true
      this.error = null

      try {
        const response = await axios.post('/api/coupons/validate', {
          code: this.couponCode.trim().toUpperCase(),
          cart_total: this.cartTotal,
        })

        if (response.data.success) {
          this.appliedCoupon = response.data.data
          this.$emit('coupon-applied', this.appliedCoupon)
          this.couponCode = ''
        }
      } catch (error) {
        if (error.response?.data?.message) {
          this.error = error.response.data.message
        } else {
          this.error = 'Failed to validate coupon'
        }
        this.$emit('coupon-error', this.error)
      } finally {
        this.loading = false
      }
    },
    removeCoupon() {
      this.appliedCoupon = null
      this.couponCode = ''
      this.error = null
      this.$emit('coupon-removed')
    },
  },
}
</script>
