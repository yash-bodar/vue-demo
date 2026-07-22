<template>
  <div class="container py-5">
    <!-- Header Section -->
    <div class="card card-premium mb-4 p-4">
      <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
        <div class="d-flex align-items-center">
          <div class="me-3 d-inline-flex align-items-center justify-content-center rounded-3 bg-primary text-white" style="width: 48px; height: 48px;">
            <i class="fas fa-shopping-cart fs-4"></i>
          </div>
          <div>
            <h5 class="mb-0 fw-bold text-dark">My Shopping Cart</h5>
            <p class="mb-0 text-muted small">Review your items and select shipping details</p>
          </div>
        </div>
        <router-link to="/product" class="btn btn-outline-primary btn-sm">
          <i class="fas fa-arrow-left me-2"></i>Continue Shopping
        </router-link>
      </div>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="text-center py-5">
      <div class="spinner-border text-primary" role="status">
        <span class="visually-hidden">Loading...</span>
      </div>
      <p class="mt-3 text-muted">Loading your cart items...</p>
    </div>

    <!-- Empty State -->
    <div v-else-if="cartProducts.length === 0" class="text-center py-5 bg-white rounded-4 shadow-sm border border-light">
      <i class="fas fa-shopping-basket fa-4x text-muted mb-3 opacity-60"></i>
      <h5 class="text-muted fw-bold">Your cart is empty</h5>
      <p class="text-muted small mb-4">You have no items in your shopping cart. Add some products to start buying!</p>
      <router-link to="/product" class="btn btn-primary px-4 py-2">
        <i class="fas fa-shopping-bag me-2"></i>Start Shopping
      </router-link>
    </div>

    <!-- Cart Layout Grid -->
    <div v-else class="row g-4">
      <!-- Left Column: Items & Delivery Addresses -->
      <div class="col-lg-8">
        <!-- Cart Items List -->
        <h5 class="fw-bold mb-3 text-dark d-flex align-items-center">
          <i class="fas fa-list me-2 text-primary"></i>Items in Cart ({{ cartProducts.length }})
        </h5>
        
        <div v-for="(cartProduct, index) in cartProducts" :key="index" class="card card-premium mb-3">
          <div class="p-3">
            <div class="row align-items-center g-3">
              <!-- Image Thumbnail -->
              <div class="col-3 col-sm-2 col-md-2">
                <div class="premium-product-img-wrapper" style="border-radius: 0.75rem;">
                  <img :src="getImageUrl(cartProduct.product.image)" class="premium-product-img" :alt="cartProduct.product.name">
                </div>
              </div>
              
              <!-- Content -->
              <div class="col-9 col-sm-10 col-md-10">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                  <!-- Name & Description -->
                  <div>
                    <h6 class="fw-bold mb-1 cursor-pointer" @click="$router.push(`/product/detail/${cartProduct.product.id}`)">
                      {{ cartProduct.product.name }}
                    </h6>
                    <!-- Variant details badge -->
                    <span v-if="cartProduct.variant" class="badge bg-light text-secondary border border-light small mb-2 d-inline-block" style="font-size: 0.7rem; font-weight: 600;">
                      Option: {{ cartProduct.variant.name }}
                    </span>
                    <p class="text-muted small mb-2 text-truncate" style="max-width: 320px;">
                      {{ cartProduct.product.description || 'No description available' }}
                    </p>
                    <span class="fw-bold text-primary">
                      {{ user?.currency_sign }}{{ (cartProduct.variant ? cartProduct.variant.converted_price : cartProduct.product.converted_price).toFixed(2) }}
                    </span>
                  </div>

                  <!-- Qty Controls & Subtotal -->
                  <div class="d-flex align-items-center justify-content-between justify-content-md-end gap-4 flex-wrap">
                    <!-- Qty Controls -->
                    <div class="premium-qty-controls">
                      <button 
                        class="qty-btn" 
                        :disabled="loadingProductId === (cartProduct.product.id + '_' + (cartProduct.product_variant_id || ''))" 
                        @click="updateCart(cartProduct.product.id, cartProduct.product_variant_id, 'decrease')"
                      >
                        <i class="fas fa-minus small"></i>
                      </button>
                      <span class="qty-display small">{{ getCartQuantity(cartProduct.product.id, cartProduct.product_variant_id) }}</span>
                      <button 
                        class="qty-btn" 
                        :disabled="loadingProductId === (cartProduct.product.id + '_' + (cartProduct.product_variant_id || '')) || getCartQuantity(cartProduct.product.id, cartProduct.product_variant_id) >= (cartProduct.variant ? cartProduct.variant.stock : cartProduct.product.stock)" 
                        @click="updateCart(cartProduct.product.id, cartProduct.product_variant_id, 'increase')"
                      >
                        <i class="fas fa-plus small"></i>
                      </button>
                    </div>

                    <!-- Total Cost -->
                    <div class="text-end" style="min-width: 100px;">
                      <span class="text-muted small d-block">Subtotal</span>
                      <span class="fw-bold text-dark">
                        {{ user?.currency_sign }}{{ ((cartProduct.variant ? cartProduct.variant.converted_price : cartProduct.product.converted_price) * cartProduct.quantity).toFixed(2) }}
                      </span>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Delivery Addresses Section -->
        <div class="mt-5">
          <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="fw-bold mb-0 text-dark">
              <i class="fas fa-map-marker-alt me-2 text-primary"></i>Delivery Address
            </h5>
            <button type="button" @click="addAddress" class="btn btn-outline-primary btn-sm">
              <i class="fas fa-plus me-1"></i>Add Address
            </button>
          </div>

          <div v-if="loadingAddr" class="text-center py-3">
            <div class="spinner-border spinner-border-sm text-primary" role="status">
              <span class="visually-hidden">Loading...</span>
            </div>
          </div>

          <div v-else class="row g-3">
            <div v-for="address in addresses" :key="address.id" class="col-12">
              <div 
                class="card card-premium" 
                :style="selectedAddressId === address.id ? 'border-color: var(--primary-color) !important; background-color: rgba(99, 102, 241, 0.02);' : ''"
                @click="selectAddress(address)"
                style="cursor: pointer;"
              >
                <div class="p-3">
                  <div class="d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center gap-3">
                      <div class="form-check m-0">
                        <input 
                          class="form-check-input" 
                          type="radio" 
                          :checked="selectedAddressId === address.id"
                          @change="selectAddress(address)"
                        >
                      </div>
                      <div>
                        <h6 class="fw-bold mb-1">
                          {{ address.full_name }}
                          <span class="badge bg-primary ms-2 small" style="font-size: 0.65rem;">{{ address.phone_number }}</span>
                          <span v-if="address.is_default" class="badge badge-primary-soft ms-2 small" style="font-size: 0.65rem;">Default</span>
                        </h6>
                        <p class="mb-0 text-muted small">
                          {{ address.address_line1 }}<span v-if="address.address_line2">, {{ address.address_line2 }}</span>, 
                          {{ address.city }}, {{ address.state }}, {{ address.country }} - {{ address.postal_code }}
                        </p>
                      </div>
                    </div>
                    <button 
                      class="btn btn-light btn-sm" 
                      style="border-radius: 50%; width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; padding: 0;"
                      @click.stop="editAddress(address)"
                    >
                      <i class="fas fa-edit text-muted"></i>
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Right Column: Sticky Summary & Payments -->
      <div class="col-lg-4">
        <div class="position-sticky" style="top: 90px; z-index: 5;">
          <h5 class="fw-bold mb-3 text-dark">
            <i class="fas fa-file-invoice-dollar me-2 text-primary"></i>Order Summary
          </h5>
          
          <div class="card card-premium p-4">
            <div class="d-flex justify-content-between mb-2">
              <span class="text-muted">Total Items</span>
              <span class="fw-bold text-dark">{{ cartProducts.reduce((sum, item) => sum + item.quantity, 0) }}</span>
            </div>
            <div class="d-flex justify-content-between mb-4">
              <span class="text-muted">Payment Type</span>
              <span class="badge bg-success-soft text-success fw-bold">Card (Stripe)</span>
            </div>
            
            <hr class="border-light mb-4">

            <div class="d-flex justify-content-between align-items-baseline mb-4">
              <span class="fs-5 fw-bold text-dark">Estimated Total:</span>
              <span class="fs-3 fw-extrabold text-primary">
                {{ user?.currency_sign || '$' }}{{ totalAmount }}
              </span>
            </div>

            <button 
              class="btn btn-primary btn-lg w-100 py-3 mb-2" 
              type="button" 
              @click="checkout" 
              :disabled="cartProducts.length === 0"
            >
              <i class="fas fa-credit-card me-2"></i>Proceed to Checkout
            </button>
            <p class="text-center text-muted small mb-0 mt-3">
              <i class="fas fa-shield-alt me-1 text-success"></i>Safe and encrypted Transactions
            </p>
          </div>
        </div>
      </div>
    </div>

    <!-- Add Address Modal -->
    <div class="modal fade" id="addAddressModal" tabindex="-1" aria-labelledby="addAddressModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-lg">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 1.25rem;">
          <div class="modal-header border-light">
            <h5 class="modal-title fw-bold" id="addAddressModalLabel">Add Delivery Address</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body p-4">
            <form id="addAddressForm">
              <div class="row">
                <div class="col-md-6">
                  <div class="mb-3">
                    <label for="fullName" class="form-label fw-bold text-muted small">Full Name *</label>
                    <input type="text" class="form-control" id="fullName" required placeholder="John Doe" v-model="addrForm.full_name">
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="mb-3">
                    <label for="phoneNumber" class="form-label fw-bold text-muted small">Phone Number *</label>
                    <input type="tel" class="form-control" id="phoneNumber" required placeholder="+1 234 567 8900" v-model="addrForm.phone">
                  </div>
                </div>
              </div>
              
              <div class="mb-3">
                <label for="addressLine1" class="form-label fw-bold text-muted small">Address Line 1 *</label>
                <input type="text" class="form-control" id="addressLine1" required placeholder="123 Main Street" v-model="addrForm.address_line1">
              </div>
              
              <div class="mb-3">
                <label for="addressLine2" class="form-label fw-bold text-muted small">Address Line 2 (Optional)</label>
                <input type="text" class="form-control" id="addressLine2" placeholder="Apartment, suite, unit, etc." v-model="addrForm.address_line2">
              </div>
              
              <div class="row">
                <div class="col-md-6">
                  <div class="mb-3">
                    <label for="city" class="form-label fw-bold text-muted small">City *</label>
                    <input type="text" class="form-control" id="city" required placeholder="New York" v-model="addrForm.city">
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="mb-3">
                    <label for="state" class="form-label fw-bold text-muted small">State *</label>
                    <input type="text" class="form-control" id="state" required placeholder="NY" v-model="addrForm.state">
                  </div>
                </div>
              </div>
              
              <div class="row">
                <div class="col-md-6">
                  <div class="mb-3">
                    <label for="postalCode" class="form-label fw-bold text-muted small">Postal Code *</label>
                    <input type="text" class="form-control" id="postalCode" required placeholder="10001" v-model="addrForm.postal_code">
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="mb-3">
                    <label for="country" class="form-label fw-bold text-muted small">Country *</label>
                    <input type="text" class="form-control" id="country" required placeholder="United States" v-model="addrForm.country">
                  </div>
                </div>
              </div>
              
              <div class="mt-3">
                <div class="form-check">
                  <input class="form-check-input" type="checkbox" id="isDefault" :checked="addrForm.is_default" v-model="addrForm.is_default">
                  <label class="form-check-label fw-semibold text-dark small" for="isDefault">
                    Set as default delivery address
                  </label>
                </div>
              </div>
            </form>
          </div>
          <div class="modal-footer border-light">
            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
            <button type="button" class="btn btn-primary" @click="saveAddress">
              <i class="fas fa-save me-2"></i>Save Address
            </button>
          </div>
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
      loadingProductId: null,
      loading: false,
      loadingAddr: false,
      addresses: [],
      selectedAddressId: null,
      addrForm: {
        id: null,
        full_name: '',
        phone: '',
        address_line1: '',
        address_line2: '',
        city: '',
        state: '',
        postal_code: '',
        country: '',
        is_default: false
      }
    }
  },
  computed: {
    ...mapState(useAuthStore, ['user']),
    totalAmount() {
      let total =  this.cartProducts.reduce((total, cartProduct) => {
        const price = cartProduct.variant ? cartProduct.variant.converted_price : cartProduct.product.converted_price;
        return total + (price * cartProduct.quantity);
      }, 0);
      return total.toFixed(2);
    }
  },
  mounted() {
    this.loadCart();
    this.loadAddresses();
  },
  methods: {
    getImageUrl,
    async updateCart(productId, variantId, action='increase') {
      const loadingKey = productId + '_' + (variantId || '');
      this.loadingProductId = loadingKey;
      
      const response = await this.$axios.post('/api/update-cart', {
        product_id: productId,
        product_variant_id: variantId,
        action: action
      });
      const data = response.data;
      if(data.success) {
        const existing = this.cartProducts.find(p => p.product_id === productId && p.product_variant_id === variantId);

        if (action === 'increase') {
          if (existing) {
            existing.quantity++;
          } else {
            this.cartProducts.push({ product_id: productId, product_variant_id: variantId, quantity: 1 });
          }
        }
        if (action === 'decrease') {
          if (existing) {
            if (existing.quantity > 1) {
              existing.quantity--;
            } else {
              this.cartProducts = this.cartProducts.filter(p => !(p.product_id === productId && p.product_variant_id === variantId));
            }
          }
        }
      }
      this.loadingProductId = null;
    },
    async loadCart() {
      this.loading = true;
      try {
        const response = await this.$axios.get('/api/cart');
        const data = response.data;
        if (data.success) {
          this.cartProducts = data.data;
        }
      } catch (error) {
        console.error('Error loading cart:', error);
      } finally {
        this.loading = false;
      }
    },  
    getCartQuantity(productId, variantId) {
      const item = this.cartProducts.find(p => p.product_id === productId && p.product_variant_id === variantId);
      return item ? item.quantity : 0;
    },
    async loadAddresses() {
      this.loadingAddr = true;
      try {
        const response = await this.$axios.get('/api/get-addresses');
        const data = response.data;
        if (data.success) {
          this.addresses = data.data;
          if (!this.selectedAddressId) {
            const defaultAddress = this.addresses.find(addr => addr.is_default === 1);
            if (defaultAddress) {
              this.selectedAddressId = defaultAddress.id;
            }
          }
          else if (!this.addresses.find(addr => addr.id === this.selectedAddressId)) {
            const defaultAddress = this.addresses.find(addr => addr.is_default === 1);
            this.selectedAddressId = defaultAddress ? defaultAddress.id : null;
          }
        }
      } catch (error) {
        console.error('Error loading addresses:', error);
      } finally {
        this.loadingAddr = false;
      }
    },
    addAddress(){
      this.addrForm = {
        id: null,
        full_name: '',
        phone: '',
        address_line1: '',
        address_line2: '',
        city: '',
        state: '',
        postal_code: '',
        country: '',
        is_default: false
      };
      $('#addAddressModal').modal('show');
    },
    async saveAddress(){
      const response = await this.$axios.post('/api/update-address', this.addrForm);
      const data = response.data;
      if(data.success) {
        this.loadAddresses();
        $('#addAddressModal').modal('hide');
      }
    },
    selectAddress(address) {
      this.selectedAddressId = address.id;
    },
    editAddress(address) {
      this.addrForm = {
        id: address.id,
        full_name: address.full_name,
        phone: address.phone_number,
        address_line1: address.address_line1,
        address_line2: address.address_line2,
        city: address.city,
        state: address.state,
        postal_code: address.postal_code,
        country: address.country,
        is_default: address.is_default === 1
      };
      $('#addAddressModal').modal('show');
    },
    async checkout(){
      if(!this.selectedAddressId) {
        alert('Please select a delivery address');
        return;
      }
      if(this.cartProducts.length === 0) {
        alert('Your cart is empty');
        return;
      }
      const outOfStockItems = this.cartProducts.filter(cp => {
        const maxStock = cp.variant ? cp.variant.stock : cp.product.stock;
        return cp.quantity > maxStock;
      });
      if(outOfStockItems.length > 0) {
        alert('Some items in your cart are out of stock');
        return;
      }
      const response = await this.$axios.post('/api/checkout', {
        address_id: this.selectedAddressId
      });
      const data = response.data;
      if(data.success) {
        this.$router.push('/checkout');
      } else {
        alert(data.message || 'Checkout failed');
      }
    }
  }
}
</script>