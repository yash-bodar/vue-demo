<template>
  <div class="cart-summary card">
    <div class="card-header bg-primary text-white">
      <h5 class="mb-0">Order Summary</h5>
    </div>
    <div class="card-body">
      <!-- Coupon Applier -->
      <CouponApplier
        :cart-total="subtotal"
        :currency="currencySymbol"
        @coupon-applied="onCouponApplied"
        @coupon-removed="onCouponRemoved"
        @coupon-error="onCouponError"
      />

      <!-- Summary Details -->
      <div class="summary-details">
        <div class="summary-row">
          <span>Subtotal:</span>
          <span>{{ currencySymbol }}{{ subtotal.toFixed(2) }}</span>
        </div>

        <div v-if="discountAmount > 0" class="summary-row text-success fw-bold">
          <span>Discount:</span>
          <span>-{{ currencySymbol }}{{ discountAmount.toFixed(2) }}</span>
        </div>

        <div class="summary-row">
          <span>Shipping:</span>
          <span>{{ currencySymbol }}{{ shippingCost.toFixed(2) }}</span>
        </div>

        <div v-if="taxAmount > 0" class="summary-row">
          <span>Tax ({{ taxRate }}%):</span>
          <span>{{ currencySymbol }}{{ taxAmount.toFixed(2) }}</span>
        </div>

        <hr />

        <div class="summary-row total">
          <span>Total:</span>
          <span>{{ currencySymbol }}{{ total.toFixed(2) }}</span>
        </div>
      </div>

      <!-- Savings Badge -->
      <div v-if="discountAmount > 0" class="alert alert-success mt-3 mb-0">
        <i class="fas fa-check-circle me-2"></i>
        You save <strong>{{ currencySymbol }}{{ discountAmount.toFixed(2) }}</strong> with this
        coupon!
      </div>
    </div>
  </div>
</template>

<script>
import CouponApplier from './CouponApplier.vue'

export default {
  name: 'CartSummary',
  components: {
    CouponApplier,
  },
  props: {
    subtotal: {
      type: Number,
      required: true,
    },
    shippingCost: {
      type: Number,
      default: 10,
    },
    taxRate: {
      type: Number,
      default: 8, // 8%
    },
    currencyCode: {
      type: String,
      default: 'USD', // USD, EUR, INR, etc.
    },
  },
  data() {
    return {
      discountAmount: 0,
      appliedCoupon: null,
    }
  },
  computed: {
    currencySymbol() {
      const symbols = {
        USD: '$',
        EUR: '€',
        INR: '₹',
        GBP: '£',
        CAD: 'C$',
        AUD: 'A$',
        AED: 'د.إ',
      }
      return symbols[this.currencyCode] || '$'
    },
    taxAmount() {
      const subtotalAfterDiscount = this.subtotal - this.discountAmount
      return (subtotalAfterDiscount * this.taxRate) / 100
    },
    total() {
      return this.subtotal - this.discountAmount + this.shippingCost + this.taxAmount
    },
  },
  methods: {
    onCouponApplied(coupon) {
      this.appliedCoupon = coupon
      this.discountAmount = coupon.discount_amount
      this.$emit('coupon-applied', {
        coupon_id: coupon.coupon_id,
        code: coupon.code,
        discount_amount: this.discountAmount,
        final_amount: this.total,
      })
    },
    onCouponRemoved() {
      this.appliedCoupon = null
      this.discountAmount = 0
      this.$emit('coupon-removed')
    },
    onCouponError(error) {
      this.$emit('coupon-error', error)
    },
  },
}
</script>
