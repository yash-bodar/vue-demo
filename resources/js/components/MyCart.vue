<template>
    <div class="cart-container">
      <!-- Cart Header -->
      <div class="cart-header bg-white m-2 border-bottom shadow-sm p-3 mx-4 my-3 rounded-2">
        <div class="row align-items-center g-3">
          <div class="col-12 col-md-6">
            <div class="d-flex align-items-center">
              <div class="me-3">
                <i class="fas fa-shopping-cart fa-2x text-primary p-1"></i>
              </div>
              <div>
                <h5 class="mb-0 fw-bold text-dark">My Cart</h5>
                <p class="mb-0 text-muted small">{{ cartProducts.length }} items</p>
              </div>
            </div>
          </div>
          <div class="col-12 col-md-6">
            <div class="row g-2 justify-content-end align-items-center">
              <div class="col-auto">
                <div class="total-amount">
                  <span class="text-muted small">Total</span>
                  <div class="fs-4 fw-bold text-primary">{{ user?.currency_sign || '$' }}{{ totalAmount }}</div>
                </div>
              </div>
              <div class="col-auto ms-1">
                <button class="btn btn-primary checkout-btn" type="button" @click="checkout" :disabled="cartProducts.length === 0">
                  <i class="fas fa-credit-card me-2"></i>Checkout
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
      <!-- Cart Body -->
      <div class="cart-body p-4">
        <!-- Loading State -->
        <div v-if="loading" class="text-center py-5">
          <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Loading...</span>
          </div>
          <p class="mt-3 text-muted">Loading your cart...</p>
        </div>
        
        <!-- Empty State -->
        <div v-else-if="cartProducts.length === 0" class="text-center py-5">
          <i class="fas fa-shopping-cart fa-4x text-muted mb-3"></i>
          <h5 class="text-muted">Your cart is empty</h5>
          <p class="text-muted mb-4">Add some products to get started!</p>
          <button class="btn btn-primary bg-primary" @click="$router.push('/product')">
            <i class="fas fa-shopping-bag me-2"></i>Browse Products
          </button>
        </div>
        
        <!-- Cart Items -->
        <div v-else class="cart-items">
          <div v-for="(cartProduct,index) in cartProducts" :key="index" class="cart-item mb-4">
            <div class="card h-100 shadow-sm border-0 rounded-3 overflow-hidden">
              <div class="card-body p-0">
                <div class="row g-0 align-items-center">
                  <!-- Product Image -->
                  <div class="col-md-3 col-lg-2">
                    <div class="cart-item-image position-relative">
                      <div class="image-placeholder d-flex align-items-center justify-content-center bg-light h-100">
                        <img :src="$getImageUrl(cartProduct.product.image)" class="cart-product-image" :alt="cartProduct.product.name">
                      </div>
                    </div>
                  </div>
                  
                  <!-- Product Details -->
                  <div class="col-md-9 col-lg-10">
                    <div class="p-3">
                      <div class="row align-items-center">
                        <div class="col-md-6">
                          <div class="product-info cursor-pointer" @click="$router.push(`/product/detail/${cartProduct.product.id}`)">
                            <h5 class="cart-product-title fw-semibold mb-2">{{ cartProduct.product.name }}</h5>
                            <p class="cart-product-description text-muted small mb-3">
                              {{ cartProduct.product.description || 'No description available' }}
                            </p>
                            <div class="cart-product-price">
                              <span class="fs-5 fw-bold text-primary">{{ user?.currency_sign }}{{ cartProduct.product.converted_price.toFixed(2) }}</span>
                              <span class="text-muted ms-2">x {{ cartProduct.quantity }}</span>
                            </div>
                          </div>
                        </div>
                        <div class="col-md-6">
                          <div class="d-flex flex-column align-items-md-end">
                            <!-- Item Total -->
                            <div class="item-total mb-3">
                              <span class="text-muted small">Item Total</span>
                              <div class="fs-5 fw-bold text-primary">
                                {{ user?.currency_sign }}{{ (cartProduct.product.converted_price * cartProduct.quantity).toFixed(2) }}
                              </div>
                            </div>
                            
                            <!-- Quantity Controls -->
                            <div class="quantity-controls-wrapper">
                              <div v-if="getCartQuantity(cartProduct.product.id) > 0" class="quantity-controls d-flex align-items-center gap-2">
                                <button class="btn btn-sm btn-primary quantity-btn" :disabled="loadingProductId === cartProduct.product.id" @click="updateCart(cartProduct.product.id,'decrease')">
                                  <i class="fas fa-minus"></i>
                                </button>
                                <span class="quantity-display fw-semibold px-3">{{ getCartQuantity(cartProduct.product.id) }}</span>
                                <button class="btn btn-sm btn-primary quantity-btn" :disabled="loadingProductId === cartProduct.product.id || getCartQuantity(cartProduct.product.id) >= cartProduct.product.stock" @click="updateCart(cartProduct.product.id,'increase')">
                                  <i class="fas fa-plus"></i>
                                </button>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Addresses Section -->
        <div class="addresses-section mt-4 ">
          <div class="d-flex justify-content-between align-items-center gap-2">
            <h5 class="fw-bold mb-0">Delivery Addresses</h5>
            <button type="button" @click="addAddress" class="btn btn-primary btn-sm text-primary fw-bold">+ Add Address</button>
          </div>
          <div v-if="loadingAddr" class="text-center py-3">
            <div class="spinner-border spinner-border-sm" role="status">
              <span class="visually-hidden">Loading...</span>
            </div>
          </div>
          <div v-else class="row g-3">
            <div v-for="address in addresses" :key="address.id" class="col-12">
              <div class="card address-card" :class="{ 'bg-primary text-light shadow': selectedAddressId === address.id, }" @click="selectAddress(address)" style="cursor: pointer;">
                <div class="card-body">
                  <div class="d-flex justify-content-between align-items-start">
                    <div class="flex-grow-1">
                      <div class="d-flex align-items-center mb-2">
                        <div class="form-check me-3" style="display: none;">
                          <input class="form-check-input" type="radio" :name="'address'" :id="'address_' + address.id" :checked="selectedAddressId === address.id" @change="selectAddress(address)">
                        </div>
                        <h6 class="fw-bold mb-0">{{ address.full_name }} - {{ address.phone_number }}</h6>
                        <span v-if="address.is_default" class="badge bg-warning text-dark ms-2">Default</span>
                      </div>
                      <p class="mb-1">{{ address.address_line1 }}<span v-if="address.address_line2">, {{ address.address_line2 }}</span></p>
                      <p class="mb-1">{{ address.city }}, {{ address.state }}, {{ address.country }} - {{ address.postal_code }}</p>
                    </div>
                    <div class="ms-3">
                      <button class="btn btn-sm p-2 rounded-2 " :class="{ 'color-primary bg-white': selectedAddressId === address.id, 'bg-primary text-white': selectedAddressId !== address.id }" @click.stop="editAddress(address)">
                        <i class="fas fa-edit"></i>
                      </button>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    
    <!-- Add Address Modal -->
    <div class="modal fade" id="addAddressModal" tabindex="-1" aria-labelledby="addAddressModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="addAddressModalLabel">Add Delivery Address</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body p-4">
            <form id="addAddressForm">
              <div class="row">
                <div class="col-md-6">
                  <div class="mb-3">
                    <label for="fullName" class="form-label fw-semibold"><i class="fas fa-user me-2 text-primary pt-1"></i>Full Name *</label>
                    <input type="text" class="form-control form-control-lg" id="fullName" name="full_name" required placeholder="John Doe" v-model="addrForm.full_name">
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="mb-3">
                    <label for="phoneNumber" class="form-label fw-semibold"><i class="fas fa-phone me-2 text-primary pt-1"></i>Phone Number *</label>
                    <input type="tel" class="form-control form-control-lg" id="phoneNumber" name="phone_number" required placeholder="+1 234 567 8900" v-model="addrForm.phone">
                  </div>
                </div>
              </div>
              
              <div class="mb-3">
                <label for="addressLine1" class="form-label fw-semibold"><i class="fas fa-home me-2 text-primary pt-1"></i>Address Line 1 *</label>
                <input type="text" class="form-control form-control-lg" id="addressLine1" name="address_line1" required placeholder="123 Main Street" v-model="addrForm.address_line1">
              </div>
              
              <div class="mb-3">
                <label for="addressLine2" class="form-label fw-semibold"><i class="fas fa-building me-2 text-primary pt-1"></i>Address Line 2</label>
                <input type="text" class="form-control form-control-lg" id="addressLine2" name="address_line2" placeholder="Apartment, suite, unit, building, floor, etc." v-model="addrForm.address_line2">
              </div>
              
              <div class="row">
                <div class="col-md-6">
                  <div class="mb-3">
                    <label for="city" class="form-label fw-semibold"><i class="fas fa-city me-2 text-primary pt-1"></i>City *</label>
                    <input type="text" class="form-control form-control-lg" id="city" name="city" required placeholder="New York" v-model="addrForm.city">
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="mb-3">
                    <label for="state" class="form-label fw-semibold"><i class="fas fa-map-marker-alt me-2 text-primary pt-1"></i>State *</label>
                    <input type="text" class="form-control form-control-lg" id="state" name="state" required placeholder="NY" v-model="addrForm.state">
                  </div>
                </div>
              </div>
              
              <div class="row">
                <div class="col-md-6">
                  <div class="mb-3">
                    <label for="postalCode" class="form-label fw-semibold"><i class="fas fa-envelope me-2 text-primary pt-1"></i>Postal Code *</label>
                    <input type="text" class="form-control form-control-lg" id="postalCode" name="postal_code" required placeholder="10001" v-model="addrForm.postal_code">
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="mb-3">
                    <label for="country" class="form-label fw-semibold"><i class="fas fa-globe me-2 text-primary pt-1"></i>Country *</label>
                    <input type="text" class="form-control form-control-lg" id="country" name="country" required placeholder="United States" v-model="addrForm.country">
                  </div>
                </div>
              </div>
              
              <div class="mb-4">
                <div class="form-check">
                  <input class="form-check-input" type="checkbox" id="isDefault" name="is_default" value="1" :checked="addrForm.is_default" v-model="addrForm.is_default">
                  <label class="form-check-label" for="isDefault">
                    <i class="fas fa-star me-2 text-warning"></i>Set as default address
                  </label>
                </div>
              </div>
            </form>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="button" class="btn btn-primary bg-primary" @click="saveAddress">
              <i class="fas fa-save me-2"></i>Save Address
            </button>
          </div>
        </div>
      </div>
    </div>
</template>

<script>
export default {
  name: 'MyCart',
  props: ['user'],
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
    totalAmount() {
      let total =  this.cartProducts.reduce((total, cartProduct) => {
        return total + (cartProduct.product.converted_price * cartProduct.quantity);
      }, 0);
      return total.toFixed(2);
    }
  },
  mounted() {
    this.loadCart();
    this.loadAddresses();
  },
  methods: {
    async updateCart(productId, action='increase') {
      this.loadingProductId = productId;
      const response = await this.$axios.post('/api/update-cart', {
        product_id: productId,
        action: action
      });
      const data = response.data;
      if(data.success) {
        const existing = this.cartProducts.find(p => p.product_id === productId);

        if (action === 'increase') {
          if (existing) {
            existing.quantity++;
          } else {
            this.cartProducts.push({ product_id: productId, quantity: 1 });
          }
        }
        if (action === 'decrease') {
          if (existing) {
            if (existing.quantity > 1) {
              existing.quantity--;
            } else {
              this.cartProducts = this.cartProducts.filter(p => p.product_id !== productId);
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
    getCartQuantity(productId) {
      const item = this.cartProducts.find(p => p.product_id === productId);
      return item ? item.quantity : 0;
    },
    async loadAddresses() {
      this.loadingAddr = true;
      try {
        const response = await this.$axios.get('/api/get-addresses');
        const data = response.data;
        if (data.success) {
          this.addresses = data.data;
          // Auto-select default address if no address is currently selected
          if (!this.selectedAddressId) {
            const defaultAddress = this.addresses.find(addr => addr.is_default === 1);
            if (defaultAddress) {
              this.selectedAddressId = defaultAddress.id;
            }
          }
          // If selected address no longer exists, select default
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
      $('#addAddressModal').modal('show');
      $('#addAddressForm')[0].reset();
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
        is_default: address.is_default
      };
      $('#addAddressModal').modal('show');
    },
    async checkout(){
      // Validation: Check if address is selected
      if(!this.selectedAddressId) {
        alert('Please select a delivery address');
        return;
      }
      // Validation: Check if cart has items
      if(this.cartProducts.length === 0) {
        alert('Your cart is empty');
        return;
      }
      // Validation: Check if any items are out of stock
      const outOfStockItems = this.cartProducts.filter(cp => cp.quantity > cp.product.stock);
      if(outOfStockItems.length > 0) {
        alert('Some items in your cart are out of stock');
        return;
      }
      // Pass selected address to checkout
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