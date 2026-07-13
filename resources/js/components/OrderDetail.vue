<template>
    <div class="orders-container bg-light">
      <!-- Orders Header -->
      <div class="cart-header bg-white m-2 border-bottom shadow-sm p-3 mx-4 my-3 rounded-2">
        <div class="row align-items-center g-3">
          <div class="col-12 col-md-6">
            <div class="d-flex align-items-center">
              <div class="me-3"><i class="fas fa-box fa-2x text-primary"></i></div>
              <div>
                <h5 class="mb-0 fw-bold text-dark">Order's Detail</h5>
                <p class="mb-0 text-muted small">View order information</p>
              </div>
            </div>
          </div>
          <div class="col-12 col-md-6">
            <div class="d-flex justify-content-end">
              <button class="btn btn-outline-primary" @click="$router.back()">
                <i class="fas fa-arrow-left me-2"></i>Back
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Orders Body -->
      <div class="orders-body p-4">
        <!-- Loading State -->
        <div v-if="loading" class="text-center py-5">
          <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Loading...</span>
          </div>
          <p class="mt-3 text-muted">Loading your order...</p>
        </div>
        
        <!-- Empty State -->
        <div v-else-if="!order || (order.length === 0)" class="text-center py-5">
          <div class="empty-state-icon"><i class="fas fa-box-open fa-5x text-muted mb-3"></i></div>
          <h5 class="text-muted">Order not found</h5>
          <p class="text-muted mb-4">The order you're looking for doesn't exist.</p>
          <button class="btn btn-primary bg-primary-linear" @click="$router.push('/orders')"><i class="fas fa-arrow-left me-2"></i>Back to Orders</button>
        </div>
        
        <!-- Orders List -->
        <div v-else class="orders-list">
          <div class="card shadow-sm rounded-2 bg-white mb-4">
            <!-- Order Header -->
            <div class="card-header bg-primary text-light p-4">
              <div class="row align-items-center">
                <div class="col-md-6">
                  <div class="d-flex align-items-center gap-3">
                    <div>
                      <h6 class="fw-bold text-light mb-0">Order #{{ order.id }}</h6>
                      <small class="text-light">{{ $formatDate(order.created_at) }}</small>
                    </div>
                  </div>
                </div>
                <div class="col-md-6 text-md-end">
                  <span class="badge fw-bold rounded-3 px-3 py-2" :class="$getOrderStatusBadgeClass(order.status)">{{ order.status }}</span>
                  <span class="badge fw-bold rounded-3 px-3 py-2 ms-2" :class="$getOrderStatusBadgeClass(order.payment_status)">{{ order.payment_status }}</span>
                </div>
              </div>
            </div>

            <!-- Shipping Address -->
            <div class="card-body p-4 border-bottom bg-light">
              <div class="row">
                <div class="col-md-6">
                  <h6 class="fw-semibold mb-3"><i class="fas fa-map-marker-alt me-2 text-primary"></i>Shipping Address</h6>
                  <div v-if="order.address">
                    <div class="address-info">
                      <div class="fw-semibold">{{ order.address?.full_name || 'N/A' }}</div>
                      <div class="text-muted"><span>{{ order.address?.address_line1 }}, </span><span>{{ order.address?.address_line2 || '' }}</span></div>
                      <div class="text-muted">{{ order.address?.city }}, {{ order.address?.state }}, {{ order.address?.country }} - {{ order.address?.postal_code }}</div>
                    </div>
                  </div>
                  <div v-else>
                    <div class="text-muted">No address provided</div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Order Items -->
            <div class="card-body p-4">
              <div v-for="item in order.items" :key="item.product_id" class="order-item mb-3 pb-3 border-bottom">
                <div class="row align-items-center">
                  <div class="col-md-2">
                    <img :src="$getImageUrl(item.product_image)" class="img-fluid rounded order-item-image" :alt="item.product_name">
                  </div>
                  <div class="col-md-6">
                    <h6 class="fw-semibold mb-1 cursor-pointer" @click="$router.push('/product/detail/' + item.product_id)">{{ item.product_name }}</h6>
                    <p class="text-muted small mb-0">
                      <span class="fw-bold">{{ order.currency_sign }}{{ item.price }}</span><span class="ms-2">x {{ item.quantity }}</span>
                    </p>
                  </div>
                  <div class="col-md-4 text-end">
                    <span class="fw-bold text-primary fs-5">{{ order.currency_sign }}{{ item.total }}</span>
                  </div>
                </div>
              </div>

              <!-- Order Total -->
              <div class="order-total mt-4 p-3 border-top bg-light rounded-2">
                <div class="row align-items-center">
                  <div class="col-md-6">
                    <span class="text-muted">{{ order.items.length }} item{{ order.items.length !== 1 ? 's' : '' }}</span>
                  </div>
                  <div class="col-md-6 text-end">
                    <span class="text-muted me-2">Total:</span><span class="fw-bold text-primary fs-4">{{ order.currency_sign }}{{ order.total_amount }}</span>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
</template>

<script>
export default {
  name: 'OrderDetail',
  data() {
    return {
      order: [],
      loading: false,
    }
  },
  mounted() {
    this.loadOrder();
  },
  methods: {
    async loadOrder() {
      this.loading = true;
      try {
        const id = this.$route.params.id;
        const response = await this.$axios.get(`/api/order-detail/${id}`);
        const data = response.data;
        console.log(data);
        if (data.success) {
          this.order = data.data;
        }
      } catch (error) {
        console.error('Error loading cart:', error);
      } finally {
        this.loading = false;
      }
    }
  }
}
</script>