<template>
  <div class="checkout-container bg-light">
    <div class="container py-5">
      <div class="row">
        <!-- Left Column - Order Summary -->
        <div class="col-lg-8">
          <div class="card mb-4 shadow-sm">
            <div class="card-header bg-white">
              <h5 class="mb-0 fw-bold"><i class="fas fa-shopping-bag me-2 text-primary"></i>Order Summary</h5>
            </div>
            <div class="card-body">
              <!-- Loading State -->
              <div v-if="loading" class="text-center py-5">
                <div class="spinner-border text-primary bg-primary" role="status">
                  <span class="visually-hidden">Loading...</span>
                </div>
                <p class="mt-3 text-muted">Loading order details...</p>
              </div>
              
              <!-- Order Items -->
              <div v-else>
                <div v-for="item in orderItems" :key="item.id" class="order-item mb-3 pb-3 border-bottom">
                  <div class="row align-items-center">
                    <div class="col-md-2">
                      <img :src="getImageUrl(item.product.image)" class="img-fluid rounded" :alt="item.product.name">
                    </div>
                    <div class="col-md-6">
                      <h6 class="fw-semibold mb-1">{{ item.product.name }}</h6>
                      <p class="text-muted small mb-0">{{ item.product.description || '' }}</p>
                    </div>
                    <div class="col-md-2 text-center">
                      <span class="text-muted">x{{ item.quantity }}</span>
                    </div>
                    <div class="col-md-2 text-end">
                      <span class="fw-bold text-primary">{{ user?.currency_sign || '$' }}{{ (item.product.converted_price * item.quantity).toFixed(2) }}</span>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Delivery Address -->
          <div class="card mb-4 shadow-sm">
            <div class="card-header bg-white">
              <h5 class="mb-0 fw-bold"><i class="fas fa-map-marker-alt me-2 text-primary"></i>Delivery Address</h5>
            </div>
            <div class="card-body">
              <div v-if="loading" class="text-center py-3">
                <div class="spinner-border spinner-border-sm" role="status">
                  <span class="visually-hidden">Loading...</span>
                </div>
              </div>
              <div v-else-if="selectedAddress">
                <h6 class="fw-bold">{{ selectedAddress.full_name }}</h6>
                <p class="mb-1">{{ selectedAddress.address_line1 }}</p>
                <p v-if="selectedAddress.address_line2" class="mb-1">{{ selectedAddress.address_line2 }}</p>
                <p class="mb-1">{{ selectedAddress.city }}, {{ selectedAddress.state }} {{ selectedAddress.postal_code }}</p>
                <p class="mb-1">{{ selectedAddress.country }}</p>
                <p class="mb-0 text-muted">{{ selectedAddress.phone_number }}</p>
              </div>
              <div v-else class="text-muted">
                No address selected
              </div>
            </div>
          </div>
        </div>

        <!-- Right Column - Payment -->
        <div class="col-lg-4">
          <div class="card shadow-sm">
            <div class="card-header bg-white">
              <h5 class="mb-0 fw-bold"><i class="fas fa-credit-card me-2 text-primary"></i>Payment</h5>
            </div>
            <div class="card-body">
              <!-- Order Total -->
              <div class="mb-4">
                <div class="d-flex justify-content-between mb-2">
                  <span class="text-muted">Subtotal</span>
                  <span>{{ user?.currency_sign || '$' }}{{ subtotal }}</span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                  <span class="text-muted">Shipping</span>
                  <span>{{ user?.currency_sign || '$' }}{{ shipping }}</span>
                </div>
                <hr>
                <div class="d-flex justify-content-between">
                  <span class="fw-bold">Total</span>
                  <span class="fw-bold text-primary fs-5">{{ user?.currency_sign || '$' }}{{ total }}</span>
                </div>
              </div>

              <!-- Stripe Card Form -->
              <div class="mb-4">
                <label class="form-label fw-semibold mb-3">Card Details</label>
                <div id="card-element" class="form-control p-3" style="min-height: 50px;"></div>
                <div id="card-errors" class="text-danger small mt-2" role="alert"></div>
              </div>

              <!-- Pay Button -->
              <button 
                class="btn btn-primary btn-lg w-100 bg-primary-linear" 
                :disabled="processing || !stripe || !elements"
                @click="processPayment"
              >
                <span v-if="processing">
                  <i class="fas fa-spinner fa-spin me-2"></i>Processing...
                </span>
                <span v-else>
                  <i class="fas fa-lock me-2"></i>Pay {{ user?.currency_sign || '$' }}{{ total }}
                </span>
              </button>

              <p class="text-center text-muted small mt-3 mb-0">
                <i class="fas fa-shield-alt me-1"></i>Your payment information is secure
              </p>
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
  computed: {
    ...mapState(useAuthStore, ['user']),
  },
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
      total: 0
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
          this.calculateTotals();
        }
      } catch (error) {
        console.error('Error loading checkout data:', error);
        alert('Error loading checkout data');
        // this.$router.push('/my-cart');
      } finally {
        this.loading = false;
      }
    },
    calculateTotals() {
      this.subtotal = this.orderItems.reduce((sum, item) => {
        return sum + (item.product.converted_price * item.quantity);
      }, 0).toFixed(2);
      this.shipping = '0.00';
      this.total = this.subtotal;
    },
    async initializeStripe() {
      try {
        const publishable_key = import.meta.env.VITE_STRIPE_PUBLIC_KEY;
        this.stripe = await loadStripe(publishable_key);
        
        const elementsResponse = await this.$axios.post('/api/create-payment-intent', {
          amount: parseFloat(this.total) * 100
        });
        
        if (elementsResponse.data.success) {
          this.clientSecret = elementsResponse.data.clientSecret;
          this.elements = this.stripe.elements({
            clientSecret: this.clientSecret
          });
          console.log(this.elements);
          this.cardElement = this.elements.create('card', {
            style: {
              base: {
                fontSize: '16px',
                color: '#424770',
                '::placeholder': {
                  color: '#aab7c4',
                },
              },
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
          address_id: this.selectedAddress.id
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
    getImageUrl
  }
}
</script>

<style scoped>

  .order-item:last-child {
    border-bottom: none !important;
    padding-bottom: 0% !important;
    margin-bottom: 0% !important;
  }

  .order-item img {
    height: 80px;
    width: 80px;
    object-fit: cover;
  }

  .order-item p {
    display: -webkit-box;
    display: box;
    -webkit-line-clamp: 2;
    line-clamp: 2;
    -webkit-box-orient: vertical;
    box-orient: vertical;
    overflow: hidden;
    min-height: 2.4rem;
    line-height: 1.2;
  }

  #card-element {
    border: 1px solid #ced4da;
    border-radius: 0.375rem;
  }
</style>
