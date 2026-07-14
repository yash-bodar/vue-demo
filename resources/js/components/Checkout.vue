<template>
  <div class="checkout-container bg-light min-vh-100 py-2">
    <div class="container">
      <!-- Checkout Header / Stepper -->
      <div class="cart-header bg-white m-0 border-bottom shadow-sm p-3 mb-4 rounded-3">
        <div class="row align-items-center g-3">
          <div class="col-12 col-md-6">
            <div class="d-flex align-items-center">
              <div class="me-3">
                <i class="fas fa-credit-card fa-2x text-primary p-1"></i>
              </div>
              <div>
                <h5 class="mb-0 fw-bold text-dark">Secure Checkout</h5>
                <p class="mb-0 text-muted small">Your transaction is safe and fully encrypted</p>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="row g-4">
        <!-- Left Column - Order Items & Delivery Info -->
        <div class="col-lg-8">
          <!-- Order Summary Card -->
          <div class="card border-0 shadow-sm rounded-3 mb-4">
            <div class="card-header bg-white border-0 py-3 d-flex align-items-center justify-content-between">
              <h5 class="mb-0 fw-bold text-dark">
                <i class="fas fa-shopping-bag me-2 text-primary"></i>Review Order Items
              </h5>
              <span class="badge bg-primary-soft text-primary px-3 py-2 rounded-pill fw-semibold">
                {{ orderItems.length }} {{ orderItems.length === 1 ? 'Item' : 'Items' }}
              </span>
            </div>
            <div class="card-body px-4">
              <!-- Loading State -->
              <div v-if="loading" class="text-center py-5">
                <div class="spinner-border text-primary" role="status">
                  <span class="visually-hidden">Loading...</span>
                </div>
                <p class="mt-3 text-muted">Loading your items...</p>
              </div>

              <!-- Order Items List -->
              <div v-else>
                <div v-for="item in orderItems" :key="item.id" class="order-item-row py-3 border-bottom">
                  <div class="row align-items-center">
                    <div class="col-3 col-md-2">
                      <div class="product-thumb-container bg-light rounded-3 p-1">
                        <img :src="getImageUrl(item.product.image)" class="img-fluid rounded-3" :alt="item.product.name">
                      </div>
                    </div>
                    <div class="col-9 col-md-6">
                      <h6 class="fw-bold text-dark mb-1">{{ item.product.name }}</h6>
                      <p class="text-muted small mb-0 text-truncate-2">{{ item.product.description || 'No description available' }}</p>
                    </div>
                    <div class="col-6 col-md-2 mt-2 mt-md-0 text-start text-md-center">
                      <span class="badge bg-light text-dark px-3 py-2 rounded-3">Qty: {{ item.quantity }}</span>
                    </div>
                    <div class="col-6 col-md-2 mt-2 mt-md-0 text-end">
                      <span class="fw-bold text-primary fs-6">
                        {{ user?.currency_sign || '$' }}{{ (item.product.converted_price * item.quantity).toFixed(2) }}
                      </span>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Delivery Address Card -->
          <div class="card border-0 shadow-sm rounded-3 mb-4">
            <div class="card-header bg-white border-0 py-3 d-flex align-items-center justify-content-between">
              <h5 class="mb-0 fw-bold text-dark">
                <i class="fas fa-map-marker-alt me-2 text-primary"></i>Shipping Address
              </h5>
              <router-link to="/my-cart" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                <i class="fas fa-edit me-1"></i>Change Address
              </router-link>
            </div>
            <div class="card-body px-4 py-3">
              <div v-if="loading" class="text-center py-3">
                <div class="spinner-border spinner-border-sm text-primary" role="status">
                  <span class="visually-hidden">Loading...</span>
                </div>
              </div>
              <div v-else-if="selectedAddress" class="d-flex align-items-start">
                <div class="address-icon-box bg-primary-soft text-primary rounded-3 p-3 me-3">
                  <i class="fas fa-truck fs-4"></i>
                </div>
                <div>
                  <h6 class="fw-bold text-dark mb-1">{{ selectedAddress.full_name }}</h6>
                  <p class="mb-1 text-secondary">{{ selectedAddress.address_line1 }}</p>
                  <p v-if="selectedAddress.address_line2" class="mb-1 text-secondary">{{ selectedAddress.address_line2 }}</p>
                  <p class="mb-1 text-secondary">{{ selectedAddress.city }}, {{ selectedAddress.state }} {{ selectedAddress.postal_code }}</p>
                  <p class="mb-2 text-secondary fw-semibold">{{ selectedAddress.country }}</p>
                  <span class="text-muted small d-block"><i class="fas fa-phone me-2"></i>{{ selectedAddress.phone_number }}</span>
                </div>
              </div>
              <div v-else class="text-center py-4">
                <div class="text-warning mb-3">
                  <i class="fas fa-exclamation-triangle fa-3x"></i>
                </div>
                <h6 class="fw-bold text-dark">No Shipping Address Selected</h6>
                <p class="text-muted small px-3">Please choose or add a delivery address to complete your checkout.</p>
                <router-link to="/my-cart" class="btn btn-primary rounded-pill px-4 mt-2">
                  <i class="fas fa-plus me-2"></i>Select Address in Cart
                </router-link>
              </div>
            </div>
          </div>
        </div>

        <!-- Right Column - Pricing Summary & Stripe Payment -->
        <div class="col-lg-4">
          <!-- Order Pricing Summary -->
          <div class="card border-0 shadow-sm rounded-3 mb-4">
            <div class="card-header bg-white border-0 py-3">
              <h5 class="mb-0 fw-bold text-dark">Order Summary</h5>
            </div>
            <div class="card-body px-4">
              <!-- Coupon Code Card -->
              <div class="coupon-section mb-4 p-3 bg-light rounded-3">
                <label class="form-label small fw-bold text-muted mb-2">PROMO CODE</label>
                <div class="input-group">
                  <input 
                    v-model="couponCode" 
                    type="text" 
                    class="form-control border-end-0 bg-white" 
                    placeholder="Enter code..."
                    :disabled="couponLoading"
                    style="border-radius: 12px 0 0 12px;"
                  >
                  <button 
                    class="btn btn-primary px-4 fw-bold" 
                    type="button" 
                    @click="applyCoupon"
                    :disabled="couponLoading || !couponCode.trim()"
                    style="border-radius: 0 12px 12px 0;"
                  >
                    <span v-if="!couponLoading">Apply</span>
                    <span v-else><i class="fas fa-spinner fa-spin"></i></span>
                  </button>
                </div>
                
                <div v-if="couponError" class="alert alert-danger-soft alert-dismissible fade show small mt-2 mb-0 py-2 px-3 border-0 rounded-3">
                  <i class="fas fa-exclamation-circle me-1"></i>{{ couponError }}
                </div>
                
                <div v-if="appliedCoupon" class="alert alert-success-soft small mt-2 mb-0 py-2 px-3 border-0 rounded-3">
                  <div class="d-flex justify-content-between align-items-center">
                    <span>
                      <i class="fas fa-check-circle me-1 text-success"></i>
                      <strong>{{ appliedCoupon.code }}</strong> applied! (-{{ user?.currency_sign || '$' }}{{ appliedCoupon.discount_amount }})
                    </span>
                    <button class="btn btn-link text-danger p-0 border-0" @click="removeCoupon" type="button">
                      <i class="fas fa-times-circle"></i>
                    </button>
                  </div>
                </div>
              </div>

              <!-- Price Breakdown -->
              <div class="price-breakdown mb-4">
                <div class="d-flex justify-content-between mb-3 text-secondary">
                  <span>Subtotal</span>
                  <span class="fw-semibold">{{ user?.currency_sign || '$' }}{{ subtotal }}</span>
                </div>
                <div v-if="appliedCoupon" class="d-flex justify-content-between mb-3 text-success fw-semibold">
                  <span>Coupon Discount</span>
                  <span>-{{ user?.currency_sign || '$' }}{{ appliedCoupon.discount_amount }}</span>
                </div>
                <div class="d-flex justify-content-between mb-3 text-secondary">
                  <span>Shipping</span>
                  <span class="text-success fw-semibold">{{ parseFloat(shipping) === 0 ? 'FREE' : (user?.currency_sign || '$') + shipping }}</span>
                </div>
                <hr class="my-3 opacity-10">
                <div class="d-flex justify-content-between align-items-center">
                  <span class="fw-bold text-dark fs-5">Total due</span>
                  <span class="fw-extrabold text-primary fs-4">{{ user?.currency_sign || '$' }}{{ finalTotal }}</span>
                </div>
              </div>
            </div>
          </div>

          <!-- Stripe Secure Payment Card -->
          <div class="card border-0 shadow-sm rounded-3 secure-card overflow-hidden">
            <div class="card-header bg-dark text-white py-3 border-0 d-flex align-items-center">
              <i class="fas fa-shield-alt text-success me-2 fs-5"></i>
              <div>
                <h6 class="mb-0 fw-bold">Secure Card Payment</h6>
                <span class="small text-muted font-xs">SSL Encrypted Checkout</span>
              </div>
            </div>
            <div class="card-body px-4 py-4">
              <!-- Stripe Card Input -->
              <div class="mb-4">
                <label class="form-label fw-bold text-secondary mb-2 small">CREDIT OR DEBIT CARD</label>
                <div class="stripe-card-wrapper">
                  <div id="card-element" class="p-3 bg-white border rounded-3"></div>
                </div>
                <div id="card-errors" class="text-danger small mt-2 fw-semibold" role="alert"></div>
              </div>

              <!-- Submit Payment Button -->
              <button 
                class="btn btn-primary btn-lg w-100 py-3 rounded-3 shadow-sm bg-primary-linear border-0 d-flex align-items-center justify-content-center" 
                :disabled="processing || !stripe || !elements || !selectedAddress"
                @click="processPayment"
              >
                <span v-if="processing">
                  <i class="fas fa-circle-notch fa-spin me-2"></i>Processing Security Payment...
                </span>
                <span v-else class="fw-bold">
                  <i class="fas fa-lock me-2"></i>Pay {{ user?.currency_sign || '$' }}{{ finalTotal }}
                </span>
              </button>

              <div class="d-flex justify-content-center gap-3 mt-4 text-muted small align-items-center">
                <i class="fab fa-cc-stripe fs-3"></i>
                <i class="fab fa-cc-visa fs-3"></i>
                <i class="fab fa-cc-mastercard fs-3"></i>
                <i class="fab fa-cc-amex fs-3"></i>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { mapState } from 'pinia'
import { useAuthStore } from '../stores/auth'
import { loadStripe } from '@stripe/stripe-js';
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
      const total = parseFloat(this.total) + parseFloat(this.shipping);
      const finalAmount = total - this.discountAmount;
      return finalAmount.toFixed(2);
    }
  },
  async mounted() {
    await this.loadCheckoutData();
    await this.initializeStripe();
  },
  methods: {
    async loadCheckoutData() {
      this.loading = true;
      try {
        const response = await this.$axios.get('/api/checkout-data');
        const data = response.data;
        if (data.success) {
          this.orderItems = data.data.cart_items;
          this.selectedAddress = data.data.address;
          this.shippingRules = data.data.shipping_rules || null;
          this.calculateTotals();
        }
      } catch (error) {
        console.error('Error loading checkout data:', error);
        alert('Error loading checkout data');
      } finally {
        this.loading = false;
      }
    },
    calculateTotals() {
      const subtotalVal = this.orderItems.reduce((sum, item) => {
        return sum + (item.product.converted_price * item.quantity);
      }, 0);
      this.subtotal = subtotalVal.toFixed(2);

      let shippingVal = 0.00;
      if (this.shippingRules) {
        for (const [range, fee] of Object.entries(this.shippingRules)) {
          const limits = range.split('-');
          if (limits.length === 2) {
            const min = parseFloat(limits[0]);
            const max = limits[1] === '*' ? Infinity : parseFloat(limits[1]);
            if (subtotalVal >= min && subtotalVal < max) {
              shippingVal = parseFloat(fee);
              break;
            }
          }
        }
      }

      this.shipping = shippingVal.toFixed(2);
      this.total = this.subtotal;
    },
    async initializeStripe() {
      try {
        const publishable_key = import.meta.env.VITE_STRIPE_PUBLIC_KEY;
        this.stripe = await loadStripe(publishable_key);
        
        const elementsResponse = await this.$axios.post('/api/create-payment-intent', {
          amount: Math.round(parseFloat(this.finalTotal) * 100)
        });
        
        if (elementsResponse.data.success) {
          this.clientSecret = elementsResponse.data.clientSecret;
          this.elements = this.stripe.elements({
            clientSecret: this.clientSecret
          });
          
          this.cardElement = this.elements.create('card', {
            style: {
              base: {
                fontSize: '16px',
                color: '#2c3e50',
                fontFamily: '"Outfit", "Inter", -apple-system, BlinkMacSystemFont, sans-serif',
                fontSmoothing: 'antialiased',
                '::placeholder': {
                  color: '#aab7c4',
                },
              },
              invalid: {
                color: '#dc3545',
                iconColor: '#dc3545'
              }
            }
          });
          
          this.cardElement.mount('#card-element');
          
          this.cardElement.on('change', (event) => {
            const displayError = document.getElementById('card-errors');
            displayError.textContent = event.error ? event.error.message : '';
          });
        } else {
          console.error('Error creating payment intent:', elementsResponse.data.message);
        }
      } catch (error) {
        console.error('Error initializing Stripe:', error);
      }
    },
    async processPayment() {
      if (!this.selectedAddress) {
        alert('Please specify a delivery address.');
        return;
      }
      this.processing = true;
      try {
        const { error, paymentIntent } = await this.stripe.confirmCardPayment(
          this.clientSecret,
          {
            payment_method: {
              card: this.cardElement,
              billing_details: {
                name: this.selectedAddress.full_name,
                email: this.user?.email
              }
            }
          }
        );

        if (error) {
          document.getElementById('card-errors').textContent = error.message;
        } else if (paymentIntent.status === 'succeeded') {
          await this.placeOrder(paymentIntent.id);
        }
      } catch (error) {
        console.error('Payment error:', error);
        alert('Payment failed. Please try again.');
      } finally {
        this.processing = false;
      }
    },
    async placeOrder(paymentIntentId) {
      try {
        const response = await this.$axios.post('/api/place-order', {
          payment_intent_id: paymentIntentId,
          address_id: this.selectedAddress.id,
          coupon_code: this.couponCode || null
        });
        const data = response.data;
        if (data.success) {
          alert('Order placed successfully!');
          this.$router.push('/my-orders');
        } else {
          alert('Failed to place order. Please contact support.');
        }
      } catch (error) {
        console.error('Error placing order:', error);
        alert('Failed to place order. Please try again.');
      }
    },
    async applyCoupon() {
      if (!this.couponCode.trim()) {
        this.couponError = 'Please enter a coupon code';
        return;
      }

      this.couponLoading = true;
      this.couponError = '';

      try {
        const response = await this.$axios.post('/api/coupons/validate', {
          coupon_code: this.couponCode.toUpperCase(),
          cart_total: parseFloat(this.subtotal)
        });

        if (response.data.success) {
          this.appliedCoupon = response.data.data;
          this.discountAmount = response.data.data.discount_amount;
          this.couponCode = response.data.data.code;
          this.couponError = '';
        }
      } catch (error) {
        const message = error.response?.data?.message || 'Invalid coupon code';
        this.couponError = message;
        this.appliedCoupon = null;
        this.discountAmount = 0;
      } finally {
        this.couponLoading = false;
      }
    },
    removeCoupon() {
      this.appliedCoupon = null;
      this.couponCode = '';
      this.discountAmount = 0;
      this.couponError = '';
    },
    getImageUrl
  }
}
</script>

<style scoped>
.stepper-wrapper {
  max-width: 500px;
  margin: 0 auto;
}
.step-icon {
  width: 32px;
  height: 32px;
  font-size: 0.9rem;
}
.step-line {
  height: 2px;
  flex: 1;
}
.product-thumb-container {
  aspect-ratio: 1;
  overflow: hidden;
  display: flex;
  align-items: center;
  justify-content: center;
}
.product-thumb-container img {
  object-fit: cover;
  width: 100%;
  height: 100%;
}
.bg-primary-soft {
  background-color: rgba(102, 126, 234, 0.15) !important;
}
.address-icon-box {
  display: flex;
  align-items: center;
  justify-content: center;
  min-width: 48px;
  height: 48px;
}
.text-truncate-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
  text-overflow: ellipsis;
}
.alert-danger-soft {
  background-color: rgba(220, 53, 69, 0.1);
  color: #dc3545;
}
.alert-success-soft {
  background-color: rgba(40, 167, 69, 0.1);
  color: #28a745;
}
.stripe-card-wrapper {
  transition: all 0.3s ease;
}
#card-element {
  border: 2px solid #e9ecef !important;
  transition: border-color 0.3s ease, box-shadow 0.3s ease;
}
#card-element.StripeElement--focus {
  border-color: #667eea !important;
  box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
}
.fw-extrabold {
  font-weight: 800;
}
.font-sm {
  font-size: 0.8rem;
}
.font-xs {
  font-size: 0.75rem;
}
</style>
