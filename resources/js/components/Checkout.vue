<template>
  <div class="container-xl py-4">
    <!-- Header Card -->
    <div class="card card-vuexy p-4 mb-4">
      <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
        <div class="d-flex align-items-center gap-3">
          <div class="badge bg-label-success rounded-3 p-3">
            <i class="fas fa-shield-check fs-3"></i>
          </div>
          <div>
            <h4 class="mb-0 fw-bold text-heading">Express Checkout</h4>
            <small class="text-muted">256-Bit SSL Encrypted Secure Gateway</small>
          </div>
        </div>
        <router-link to="/my-cart" class="btn btn-outline-primary rounded-pill px-4">
          <i class="fas fa-arrow-left me-2"></i>Back to Cart
        </router-link>
      </div>
    </div>

    <!-- Main Checkout Grid -->
    <div class="row g-4">
      <!-- Left Column: Items & Shipping Address -->
      <div class="col-lg-8">
        <!-- Order Items Breakdown -->
        <div class="card card-vuexy p-4 mb-4">
          <div class="d-flex align-items-center justify-content-between mb-3">
            <h5 class="fw-bold text-heading m-0">
              <i class="fas fa-bag-shopping me-2 text-primary"></i>Review Order Items
            </h5>
            <span class="badge bg-label-primary fs-8">{{ orderItems.length }} Item(s)</span>
          </div>

          <div v-if="loading" class="text-center py-4">
            <div class="spinner-border text-primary" role="status"></div>
          </div>

          <div v-else class="d-flex flex-column gap-3">
            <div
              v-for="item in orderItems"
              :key="item.id"
              class="p-3 rounded-3 bg-light border-subtle border"
            >
              <div class="row align-items-center g-3">
                <div class="col-3 col-sm-2">
                  <img
                    :src="getImageUrl(item.product.image)"
                    class="rounded-3 img-fluid border product-thumb-sq"
                    :alt="item.product.name"
                  />
                </div>
                <div class="col-9 col-sm-10">
                  <div
                    class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2"
                  >
                    <div>
                      <h6 class="fw-bold text-heading mb-1">{{ item.product.name }}</h6>
                      <small class="text-muted fs-8 text-truncate d-block text-truncate-320">{{
                        item.product.description || 'No description'
                      }}</small>
                    </div>
                    <div class="d-flex align-items-center gap-3">
                      <span class="badge bg-label-secondary fs-8">Qty: {{ item.quantity }}</span>
                      <span class="fw-bold text-primary fs-6">
                        {{ user?.currency_sign || '$'
                        }}{{ (item.product.converted_price * item.quantity).toFixed(2) }}
                      </span>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Shipping Address Card -->
        <div class="card card-vuexy p-4 mb-4">
          <div class="d-flex align-items-center justify-content-between mb-3">
            <h5 class="fw-bold text-heading m-0">
              <i class="fas fa-location-dot me-2 text-primary"></i>Shipping Address
            </h5>
            <router-link to="/my-cart" class="btn btn-sm btn-label-primary rounded-pill"
              >Change Address</router-link
            >
          </div>

          <div v-if="loading" class="text-center py-3">
            <div class="spinner-border spinner-border-sm text-primary" role="status"></div>
          </div>

          <div
            v-else-if="selectedAddress"
            class="p-3 rounded-3 bg-label-primary border border-primary"
          >
            <h6 class="fw-bold text-heading mb-1">{{ selectedAddress.full_name }}</h6>
            <p class="text-muted fs-7 mb-1">
              {{ selectedAddress.address_line1 }} {{ selectedAddress.address_line2 }}
            </p>
            <p class="text-muted fs-7 mb-1">
              {{ selectedAddress.city }}, {{ selectedAddress.state }}
              {{ selectedAddress.postal_code }}
            </p>
            <small class="text-primary fw-semibold"
              ><i class="fas fa-phone me-1"></i>{{ selectedAddress.phone_number }}</small
            >
          </div>

          <div v-else class="text-center py-4 bg-light rounded-3">
            <i class="fas fa-triangle-exclamation text-warning fs-3 mb-2"></i>
            <h6 class="fw-bold text-heading mb-1">No Delivery Address Selected</h6>
            <router-link to="/my-cart" class="btn btn-sm btn-primary rounded-pill mt-2"
              >Select Address in Cart</router-link
            >
          </div>
        </div>
      </div>

      <!-- Right Column: Summary & Stripe Payment -->
      <div class="col-lg-4">
        <!-- Order Summary Card -->
        <div class="card card-vuexy p-4 mb-4">
          <h5 class="fw-bold text-heading mb-3">Payment Breakdown</h5>

          <!-- Promo Code -->
          <div class="mb-3">
            <div class="input-group input-group-sm">
              <input
                v-model="couponCode"
                type="text"
                class="form-control"
                placeholder="Promo Code"
                :disabled="couponLoading || !!appliedCoupon"
              />
              <button
                v-if="!appliedCoupon"
                class="btn btn-primary"
                type="button"
                @click="applyCoupon"
                :disabled="couponLoading || !couponCode.trim()"
              >
                Apply
              </button>
              <button v-else class="btn btn-danger" type="button" @click="removeCoupon">
                Remove
              </button>
            </div>
            <small v-if="couponError" class="text-danger fs-9 mt-1 d-block fw-semibold">{{
              couponError
            }}</small>
            <small v-if="appliedCoupon" class="text-success fs-9 mt-1 d-block fw-semibold">
              <i class="fas fa-check-circle me-1"></i>Applied: -{{ user?.currency_sign || '$'
              }}{{ appliedCoupon.discount_amount }}
            </small>
          </div>

          <div class="d-flex flex-column gap-2 border-bottom pb-3 mb-3 fs-7">
            <div class="d-flex justify-content-between">
              <span class="text-muted">Subtotal</span>
              <span class="fw-bold text-heading"
                >{{ user?.currency_sign || '$' }}{{ subtotal }}</span
              >
            </div>
            <div v-if="appliedCoupon" class="d-flex justify-content-between text-success">
              <span>Coupon Discount</span>
              <span class="fw-bold"
                >-{{ user?.currency_sign || '$' }}{{ appliedCoupon.discount_amount }}</span
              >
            </div>
            <div class="d-flex justify-content-between">
              <span class="text-muted">Shipping Fee</span>
              <span class="fw-bold text-success">{{
                parseFloat(shipping) === 0 ? 'FREE' : (user?.currency_sign || '$') + shipping
              }}</span>
            </div>
          </div>

          <div class="d-flex justify-content-between align-items-center mb-4">
            <span class="fw-bold text-heading fs-6">Total Amount</span>
            <span class="fw-bold text-primary fs-4"
              >{{ user?.currency_sign || '$' }}{{ finalTotal }}</span
            >
          </div>
        </div>

        <!-- Stripe Card Payment Card -->
        <div class="card card-vuexy p-4">
          <h5 class="fw-bold text-heading mb-3">
            <i class="fas fa-credit-card me-2 text-primary"></i>Credit / Debit Card
          </h5>

          <div class="mb-4">
            <div id="card-element" class="p-3 bg-card border rounded-3"></div>
            <div id="card-errors" class="text-danger fs-8 mt-2 fw-semibold" role="alert"></div>
          </div>

          <button
            class="btn btn-primary w-100 rounded-pill py-2.5 fw-bold shadow-primary"
            :disabled="processing || !stripe || !elements || !selectedAddress"
            @click="processPayment"
          >
            <span v-if="processing">
              <i class="fas fa-spinner fa-spin me-2"></i>Processing Payment...
            </span>
            <span v-else>
              <i class="fas fa-lock me-2"></i>Pay {{ user?.currency_sign || '$' }}{{ finalTotal }}
            </span>
          </button>

          <div class="d-flex justify-content-center gap-3 mt-4 text-muted fs-4">
            <i class="fab fa-cc-stripe"></i>
            <i class="fab fa-cc-visa"></i>
            <i class="fab fa-cc-mastercard"></i>
            <i class="fab fa-cc-apple-pay"></i>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { mapState } from 'pinia'
import { useAuthStore } from '../stores/auth'
import { loadStripe } from '@stripe/stripe-js'
import { getImageUrl } from '../utils/ImageUrl'

export default {
  name: 'Checkout',
  data() {
    return {
      orderItems: [],
      selectedAddress: null,
      loading: true,
      processing: false,
      stripe: null,
      elements: null,
      cardElement: null,
      clientSecret: null,
      subtotal: 0,
      shipping: 0,
      total: 0,
      couponCode: '',
      appliedCoupon: null,
      couponLoading: false,
      couponError: '',
      discountAmount: 0,
      shippingRules: null,
    }
  },
  computed: {
    ...mapState(useAuthStore, ['user']),
    finalTotal() {
      const total = parseFloat(this.total) + parseFloat(this.shipping)
      const finalAmount = total - this.discountAmount
      return finalAmount.toFixed(2)
    },
  },
  async mounted() {
    await this.loadCheckoutData()
    // YB - 2026-07-27: Auto-restore coupon passed from MyCart via route query
    if (this.$route.query.coupon) {
      this.couponCode = this.$route.query.coupon
      await this.applyCoupon()
    }
    await this.initializeStripe()
  },
  methods: {
    getImageUrl,
    async loadCheckoutData() {
      this.loading = true
      try {
        const response = await this.$axios.get('/api/checkout-data')
        const data = response.data
        if (data.success) {
          this.orderItems = data.data.cart_items || []
          this.selectedAddress = data.data.address || null
          this.shippingRules = data.data.shipping_rules || null
          this.calculateTotals()
        }
      } catch (error) {
        console.error('Error loading checkout data:', error)
      } finally {
        this.loading = false
      }
    },
    calculateTotals() {
      const subtotalVal = this.orderItems.reduce((sum, item) => {
        return sum + item.product.converted_price * item.quantity
      }, 0)
      this.subtotal = subtotalVal.toFixed(2)

      let shippingVal = 0.0
      if (this.shippingRules) {
        for (const [range, fee] of Object.entries(this.shippingRules)) {
          const limits = range.split('-')
          if (limits.length === 2) {
            const min = parseFloat(limits[0])
            const max = limits[1] === '*' ? Infinity : parseFloat(limits[1])
            if (subtotalVal >= min && subtotalVal < max) {
              shippingVal = parseFloat(fee)
              break
            }
          }
        }
      }

      this.shipping = shippingVal.toFixed(2)
      this.total = this.subtotal
    },
    async initializeStripe() {
      try {
        const publishable_key = import.meta.env.VITE_STRIPE_PUBLIC_KEY
        this.stripe = await loadStripe(publishable_key)

        const elementsResponse = await this.$axios.post('/api/create-payment-intent', {
          amount: Math.round(parseFloat(this.finalTotal) * 100),
        })

        if (elementsResponse.data.success) {
          this.clientSecret = elementsResponse.data.clientSecret
          this.elements = this.stripe.elements({
            clientSecret: this.clientSecret,
          })

          this.cardElement = this.elements.create('card', {
            style: {
              base: {
                fontSize: '15px',
                color: '#4b465c',
                fontFamily: '"Inter", sans-serif',
                '::placeholder': { color: '#82868b' },
              },
              invalid: { color: '#ea5455' },
            },
          })

          this.cardElement.mount('#card-element')

          this.cardElement.on('change', (event) => {
            const displayError = document.getElementById('card-errors')
            displayError.textContent = event.error ? event.error.message : ''
          })
        }
      } catch (error) {
        console.error('Error initializing Stripe:', error)
      }
    },
    async processPayment() {
      if (!this.selectedAddress) {
        alert('Please specify a delivery address.')
        return
      }
      this.processing = true
      try {
        const { error, paymentIntent } = await this.stripe.confirmCardPayment(this.clientSecret, {
          payment_method: {
            card: this.cardElement,
            billing_details: {
              name: this.selectedAddress.full_name,
              email: this.user?.email,
            },
          },
        })

        if (error) {
          document.getElementById('card-errors').textContent = error.message
        } else if (paymentIntent.status === 'succeeded') {
          await this.placeOrder(paymentIntent.id)
        }
      } catch (error) {
        console.error('Payment error:', error)
        alert('Payment failed. Please try again.')
      } finally {
        this.processing = false
      }
    },
    async placeOrder(paymentIntentId) {
      try {
        const response = await this.$axios.post('/api/place-order', {
          payment_intent_id: paymentIntentId,
          address_id: this.selectedAddress.id,
          coupon_code: this.couponCode || null,
        })
        if (response.data.success) {
          alert('Order placed successfully!')
          this.$router.push('/my-orders')
        } else {
          alert('Failed to place order.')
        }
      } catch (error) {
        console.error('Error placing order:', error)
        alert('Failed to place order.')
      }
    },
    async applyCoupon() {
      if (!this.couponCode.trim()) return
      this.couponLoading = true
      this.couponError = ''
      try {
        const response = await this.$axios.post('/api/coupons/validate', {
          coupon_code: this.couponCode.toUpperCase(),
          cart_total: parseFloat(this.subtotal),
        })
        if (response.data.success) {
          this.appliedCoupon = response.data.data
          this.discountAmount = response.data.data.discount_amount
          this.couponCode = response.data.data.code
        }
      } catch (error) {
        this.couponError = error.response?.data?.message || 'Invalid coupon code'
        this.appliedCoupon = null
        this.discountAmount = 0
      } finally {
        this.couponLoading = false
      }
    },
    removeCoupon() {
      this.appliedCoupon = null
      this.couponCode = ''
      this.discountAmount = 0
      this.couponError = ''
    },
  },
}
</script>
