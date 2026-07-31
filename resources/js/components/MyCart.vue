<template>
  <div class="container-xl py-4">
    <!-- Header Card -->
    <div class="card card-vuexy p-4 mb-4">
      <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
        <div class="d-flex align-items-center gap-3">
          <div class="badge bg-label-primary rounded-3 p-3">
            <i class="fas fa-cart-shopping fs-3"></i>
          </div>
          <div>
            <h4 class="mb-0 fw-bold text-heading">Shopping Cart</h4>
            <small class="text-muted">Review items & select delivery details</small>
          </div>
        </div>
        <router-link to="/product" class="btn btn-outline-primary rounded-pill px-4">
          <i class="fas fa-arrow-left me-2"></i>Continue Shopping
        </router-link>
      </div>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="card card-vuexy p-5 text-center">
      <div class="spinner-border text-primary mx-auto mb-3" role="status"></div>
      <p class="text-muted m-0">Loading your cart items...</p>
    </div>

    <!-- Empty Cart State -->
    <div v-else-if="cartProducts.length === 0" class="card card-vuexy p-5 text-center">
      <i class="fas fa-basket-shopping fa-4x text-muted mb-3 opacity-50"></i>
      <h4 class="fw-bold text-heading">Your Shopping Cart is Empty</h4>
      <p class="text-muted fs-7 mb-4">
        Discover our catalog items and add your favorite goods to cart.
      </p>
      <router-link to="/product" class="btn btn-primary rounded-pill px-4 py-2 mx-auto">
        <i class="fas fa-store me-2"></i>Browse Products
      </router-link>
    </div>

    <!-- Main Cart Layout Grid -->
    <div v-else class="row g-4">
      <!-- Left Column: Items List & Address Picker -->
      <div class="col-lg-8">
        <div class="card card-vuexy p-4 mb-4">
          <h5 class="fw-bold text-heading mb-3">Items in Your Cart ({{ cartProducts.length }})</h5>

          <div class="d-flex flex-column gap-3">
            <div
              v-for="(cartProduct, index) in cartProducts"
              :key="index"
              class="p-3 rounded-3 bg-light border-subtle border"
            >
              <div class="row align-items-center g-3">
                <!-- Thumbnail -->
                <div class="col-3 col-sm-2">
                  <img
                    :src="getImageUrl(cartProduct.product.image)"
                    class="rounded-3 img-fluid border product-thumb-sq"
                    :alt="cartProduct.product.name"
                  />
                </div>

                <!-- Product Information -->
                <div class="col-9 col-sm-10">
                  <div
                    class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3"
                  >
                    <div>
                      <h6
                        class="fw-bold text-heading cursor-pointer mb-1"
                        @click="$router.push(`/product/detail/${cartProduct.product.id}`)"
                      >
                        {{ cartProduct.product.name }}
                      </h6>
                      <span v-if="cartProduct.variant" class="badge bg-label-primary fs-9 mb-1">
                        Option: {{ cartProduct.variant.name }}
                      </span>
                      <div class="fw-bold text-primary fs-6">
                        {{ user?.currency_sign || '$'
                        }}{{
                          (cartProduct.variant
                            ? cartProduct.variant.converted_price || cartProduct.variant.price
                            : cartProduct.product.converted_price || cartProduct.product.price
                          ).toFixed(2)
                        }}
                      </div>
                    </div>

                    <!-- Quantity Adjuster & Line Total -->
                    <div class="d-flex align-items-center gap-4">
                      <div class="d-flex align-items-center border rounded-pill p-1 bg-card">
                        <button
                          class="btn btn-sm btn-icon border-0 text-primary"
                          :disabled="
                            loadingProductId ===
                            cartProduct.product.id + '_' + (cartProduct.product_variant_id || '')
                          "
                          @click="
                            updateCart(
                              cartProduct.product.id,
                              cartProduct.product_variant_id,
                              'decrease'
                            )
                          "
                        >
                          <i class="fas fa-minus fs-9"></i>
                        </button>
                        <span class="fw-bold px-3 fs-7">{{
                          getCartQuantity(cartProduct.product.id, cartProduct.product_variant_id)
                        }}</span>
                        <button
                          class="btn btn-sm btn-icon border-0 text-primary"
                          :disabled="
                            loadingProductId ===
                              cartProduct.product.id +
                                '_' +
                                (cartProduct.product_variant_id || '') ||
                            getCartQuantity(
                              cartProduct.product.id,
                              cartProduct.product_variant_id
                            ) >=
                              (cartProduct.variant
                                ? cartProduct.variant.stock
                                : cartProduct.product.stock)
                          "
                          @click="
                            updateCart(
                              cartProduct.product.id,
                              cartProduct.product_variant_id,
                              'increase'
                            )
                          "
                        >
                          <i class="fas fa-plus fs-9"></i>
                        </button>
                      </div>

                      <div class="text-end">
                        <small class="text-muted d-block fs-9">Subtotal</small>
                        <span class="fw-bold text-heading fs-6">
                          {{ user?.currency_sign || '$'
                          }}{{
                            (
                              (cartProduct.variant
                                ? cartProduct.variant.converted_price || cartProduct.variant.price
                                : cartProduct.product.converted_price ||
                                  cartProduct.product.price) * cartProduct.quantity
                            ).toFixed(2)
                          }}
                        </span>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Delivery Addresses Selection -->
        <div class="card card-vuexy p-4">
          <div class="d-flex align-items-center justify-content-between mb-3">
            <h5 class="fw-bold text-heading m-0">
              <i class="fas fa-location-dot me-2 text-primary"></i>Delivery Address
            </h5>
            <button
              type="button"
              @click="addAddress"
              class="btn btn-sm btn-outline-primary rounded-pill"
            >
              <i class="fas fa-plus me-1"></i>Add New Address
            </button>
          </div>

          <div v-if="loadingAddr" class="text-center py-3">
            <div class="spinner-border spinner-border-sm text-primary" role="status"></div>
          </div>

          <div v-else class="d-flex flex-column gap-2">
            <div
              v-for="address in addresses"
              :key="address.id"
              class="p-3 rounded-3 border cursor-pointer transition-fast"
              :class="
                selectedAddressId === address.id
                  ? 'border-primary bg-label-primary'
                  : 'bg-light border-subtle'
              "
              @click="selectAddress(address)"
            >
              <div class="d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-3">
                  <input
                    class="form-check-input"
                    type="radio"
                    :checked="selectedAddressId === address.id"
                    @change="selectAddress(address)"
                  />
                  <div>
                    <h6 class="fw-bold m-0 fs-7">
                      {{ address.title || address.full_name || 'Shipping Address' }}
                    </h6>
                    <p class="text-muted fs-8 mb-0">
                      {{ address.address_line_1 || address.address_line1 }}, {{ address.city }},
                      {{ address.state }} {{ address.postal_code }}
                    </p>
                  </div>
                </div>
                <span v-if="address.is_default" class="badge bg-primary fs-9">Default</span>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Right Column: Order Summary & Coupon -->
      <div class="col-lg-4">
        <div class="card card-vuexy p-4 mb-4">
          <h5 class="fw-bold text-heading mb-3">Order Summary</h5>

          <div class="d-flex flex-column gap-2 border-bottom pb-3 mb-3 fs-7">
            <div class="d-flex justify-content-between">
              <span class="text-muted">Bag Subtotal</span>
              <span class="fw-bold text-heading"
                >{{ user?.currency_sign || '$' }}{{ subtotal.toFixed(2) }}</span
              >
            </div>
            <div class="d-flex justify-content-between" v-if="appliedCoupon">
              <span class="text-muted">Coupon Discount</span>
              <span class="fw-bold text-success"
                >-{{ user?.currency_sign || '$' }}{{ discountAmount.toFixed(2) }}</span
              >
            </div>
            <div class="d-flex justify-content-between">
              <span class="text-muted">Estimated Shipping</span>
              <span class="fw-bold text-success">FREE</span>
            </div>
          </div>

          <div class="d-flex justify-content-between align-items-center mb-4">
            <span class="fw-bold text-heading fs-6">Order Total</span>
            <span class="fw-bold text-primary fs-4"
              >{{ user?.currency_sign || '$' }}{{ total.toFixed(2) }}</span
            >
          </div>

          <!-- Coupon Input -->
          <div class="mb-4">
            <div class="input-group input-group-sm">
              <input
                type="text"
                class="form-control"
                v-model="couponCode"
                placeholder="Promo / Coupon Code"
                :disabled="!!appliedCoupon"
              />
              <button
                v-if="!appliedCoupon"
                class="btn btn-primary"
                type="button"
                @click="applyCoupon"
              >
                Apply
              </button>
              <button v-else class="btn btn-danger" type="button" @click="removeCoupon">
                Remove
              </button>
            </div>
            <small
              v-if="couponMessage"
              :class="couponSuccess ? 'text-success' : 'text-danger'"
              class="d-block mt-1 fs-9 fw-semibold"
            >
              {{ couponMessage }}
            </small>
          </div>

          <!-- Checkout Action Button -->
          <button
            class="btn btn-primary w-100 rounded-pill py-2.5 fw-bold shadow-primary"
            @click="checkoutPage"
          >
            Proceed to Checkout <i class="fas fa-arrow-right ms-2"></i>
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { mapState } from 'pinia'
import { useAuthStore } from '../stores/auth'
import { getImageUrl } from '../utils/ImageUrl'

export default {
  name: 'MyCart',
  data() {
    return {
      cartProducts: [],
      loading: false,
      loadingProductId: null,
      addresses: [],
      loadingAddr: false,
      selectedAddressId: null,
      couponCode: '',
      couponMessage: '',
      couponSuccess: false,
      appliedCoupon: null,
    }
  },
  computed: {
    ...mapState(useAuthStore, ['user']),
    subtotal() {
      return this.cartProducts.reduce((sum, item) => {
        const price = item.variant
          ? item.variant.converted_price || item.variant.price
          : item.product.converted_price || item.product.price
        return sum + price * item.quantity
      }, 0)
    },
    discountAmount() {
      if (!this.appliedCoupon) return 0
      if (
        this.appliedCoupon.discount_type === 'percentage' ||
        this.appliedCoupon.type === 'percentage'
      ) {
        return (
          (this.subtotal * (this.appliedCoupon.discount_value || this.appliedCoupon.value)) / 100
        )
      }
      return parseFloat(
        this.appliedCoupon.discount_amount || this.appliedCoupon.discount_value || 0
      )
    },
    total() {
      return Math.max(0, this.subtotal - this.discountAmount)
    },
  },
  async mounted() {
    await this.fetchCart()
    await this.fetchAddresses()
  },
  methods: {
    getImageUrl,
    async fetchCart() {
      this.loading = true
      try {
        const response = await this.$axios.get('/api/cart')
        if (response.data.success) {
          this.cartProducts = response.data.data || []
        }
      } catch (err) {
        console.error('Fetch cart error:', err)
      } finally {
        this.loading = false
      }
    },
    async fetchAddresses() {
      this.loadingAddr = true
      try {
        const response = await this.$axios.get('/api/get-addresses')
        if (response.data.success) {
          this.addresses = response.data.data || []
          if (this.addresses.length > 0) {
            const def = this.addresses.find((a) => a.is_default)
            this.selectedAddressId = def ? def.id : this.addresses[0].id
          }
        }
      } catch (err) {
        console.error('Fetch addresses error:', err)
      } finally {
        this.loadingAddr = false
      }
    },
    getCartQuantity(productId, variantId) {
      const item = this.cartProducts.find(
        (c) => c.product_id === productId && c.product_variant_id === variantId
      )
      return item ? item.quantity : 0
    },
    async updateCart(productId, variantId, action) {
      this.loadingProductId = productId + '_' + (variantId || '')
      try {
        const response = await this.$axios.post('/api/update-cart', {
          product_id: productId,
          variant_id: variantId,
          action: action,
        })
        if (response.data.success) {
          await this.fetchCart()
        }
      } catch (err) {
        console.error('Update cart error:', err)
      } finally {
        this.loadingProductId = null
      }
    },
    selectAddress(addr) {
      this.selectedAddressId = addr.id
    },
    addAddress() {
      this.$router.push('/profile')
    },
    async applyCoupon() {
      if (!this.couponCode.trim()) return
      try {
        const response = await this.$axios.post('/api/coupons/validate', {
          coupon_code: this.couponCode.toUpperCase(),
          cart_total: parseFloat(this.subtotal),
        })
        if (response.data.success) {
          this.appliedCoupon = response.data.data
          this.couponSuccess = true
          this.couponMessage = 'Coupon code applied successfully!'
        } else {
          this.couponSuccess = false
          this.couponMessage = response.data.message || 'Invalid coupon code'
        }
      } catch (err) {
        this.couponSuccess = false
        this.couponMessage = err.response?.data?.message || 'Failed to apply coupon'
      }
    },
    removeCoupon() {
      this.appliedCoupon = null
      this.couponCode = ''
      this.couponMessage = ''
    },
    checkoutPage() {
      const query = { address_id: this.selectedAddressId }
      if (this.appliedCoupon) {
        query.coupon = this.appliedCoupon.code || this.couponCode
      }
      this.$router.push({ path: '/checkout', query })
    },
  },
}
</script>
