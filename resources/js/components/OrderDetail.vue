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
    <div class="orders-body p-4 pt-3">
      <!-- Loading State -->
      <div v-if="loading" class="text-center py-5">
        <div class="spinner-border text-primary" role="status">
          <span class="visually-hidden">Loading...</span>
        </div>
        <p class="mt-3 text-muted">Loading your order...</p>
      </div>

      <!-- Empty State -->
      <div v-else-if="!order || order.length === 0" class="text-center py-5">
        <div class="empty-state-icon"><i class="fas fa-box-open fa-5x text-muted mb-3"></i></div>
        <h5 class="text-muted">Order not found</h5>
        <p class="text-muted mb-4">The order you're looking for doesn't exist.</p>
        <button class="btn btn-primary bg-primary-linear" @click="$router.push('/orders')">
          <i class="fas fa-arrow-left me-2"></i>Back to Orders
        </button>
      </div>

      <!-- Orders List -->
      <div v-else class="orders-list">
        <!-- Header -->
        <div class="card shadow-sm border-0 mb-4">
          <div class="card-body">
            <div class="d-flex justify-content-between align-items-center flex-wrap">
              <div>
                <h5 class="fw-bold mb-1">Order #{{ order.id }}</h5>
                <div class="text-muted">Placed on {{ formatDate(order.created_at) }}</div>
              </div>
              <div>
                <span
                  class="badge rounded-3 px-3 py-2 me-2"
                  :class="getOrderStatusBadgeClass(order.status)"
                >
                  {{ order.status }}
                </span>
                <span
                  class="badge rounded-3 px-3 py-2"
                  :class="getOrderStatusBadgeClass(order.payment_status)"
                >
                  {{ order.payment_status }}
                </span>
              </div>
            </div>
          </div>
        </div>
        <!-- Summary -->
        <div class="row g-3 mb-4">
          <div class="col-lg-3 col-sm-6">
            <div class="card shadow-sm border-0 text-center">
              <div class="card-body">
                <small class="text-muted">Total Amount</small>
                <h3 class="text-primary mt-2 fw-bold">
                  {{ order.currency_sign }}{{ order.final_amount }}
                </h3>
              </div>
            </div>
          </div>

          <div class="col-lg-3 col-sm-6">
            <div class="card shadow-sm border-0 text-center">
              <div class="card-body">
                <small class="text-muted">Products</small>
                <h3 class="mt-2 fw-bold">{{ order.order_items?.length || 0 }}</h3>
              </div>
            </div>
          </div>

          <div class="col-lg-3 col-sm-6">
            <div class="card shadow-sm border-0 text-center">
              <div class="card-body">
                <small class="text-muted">Payment</small>
                <h5 class="mt-2 fw-bold">{{ order.payment_status }}</h5>
              </div>
            </div>
          </div>

          <div class="col-lg-3 col-sm-6">
            <div class="card shadow-sm border-0 text-center">
              <div class="card-body">
                <small class="text-muted">Order Status</small>
                <h5 class="mt-2 fw-bold">{{ order.status }}</h5>
              </div>
            </div>
          </div>
        </div>
        <!-- Items -->
        <div class="card shadow-sm border-0 mb-4">
          <div class="card-header bg-white p-3">
            <h5 class="fw-bold mb-0">Ordered Items</h5>
          </div>
          <div class="card-body pt-4">
            <div
              v-for="item in order.order_items || order.items || []"
              :key="item.id"
              class="row align-items-center review-item mx-0 mb-3 p-3 bg-light rounded-3"
            >
              <div class="col-3 col-md-2 text-center">
                <img
                  :src="getImageUrl(item.product?.image || item.image)"
                  class="img-fluid rounded product-thumb-60"
                />
              </div>
              <div class="col-9 col-md-7">
                <h5
                  class="fw-semibold cursor-pointer mb-1"
                  @click="item.product_id && $router.push('/product/detail/' + item.product_id)"
                >
                  {{
                    item.product?.name ||
                    item.product_name ||
                    item.name ||
                    'Product #' + (item.product_id || item.id)
                  }}
                  <span
                    v-if="item.variant || item.variant_name"
                    class="badge bg-secondary-soft text-muted small ms-2 fs-9"
                  >
                    {{ item.variant?.name || item.variant_name }}
                  </span>
                </h5>
                <div class="text-muted fs-8">
                  {{ order.currency_sign || '$' }}{{ item.price }} × {{ item.quantity }}
                </div>
                <div class="d-md-none fw-bold text-primary mt-1 fs-6">
                  {{ order.currency_sign || '$' }}{{ (item.price * item.quantity).toFixed(2) }}
                </div>
              </div>

              <div class="col-md-3 text-end d-none d-md-block">
                <div class="fw-bold fs-5 text-primary">
                  {{ order.currency_sign || '$' }}{{ (item.price * item.quantity).toFixed(2) }}
                </div>
              </div>
            </div>
          </div>
        </div>
        <!-- Address & Summary -->
        <div class="row">
          <div class="col-lg-6 mb-4">
            <div class="card shadow-sm border-0 h-100">
              <div class="card-header bg-white p-3">
                <h5 class="mb-0">
                  <i class="fas fa-map-marker-alt text-primary me-2"></i>
                  Shipping Address
                </h5>
              </div>
              <div class="card-body">
                <h6 class="fw-bold">
                  {{ order.address?.full_name }}
                </h6>
                <div>{{ order.address?.address_line1 }}</div>
                <div>{{ order.address?.address_line2 }}</div>
                <div>
                  {{ order.address?.city }},
                  {{ order.address?.state }}
                </div>
                <div>
                  {{ order.address?.country }}
                  -
                  {{ order.address?.postal_code }}
                </div>
              </div>
            </div>
          </div>
          <div class="col-lg-6 mb-4">
            <div class="card shadow-sm border-0">
              <div class="card-header bg-white p-3">
                <h5 class="mb-0">Payment Summary</h5>
              </div>
              <div class="card-body">
                <div class="d-flex justify-content-between mb-3">
                  <span>Subtotal</span>
                  <strong> {{ order.currency_sign }}{{ order.total_amount }} </strong>
                </div>
                <div class="d-flex justify-content-between mb-3">
                  <span>Shipping</span>
                  <strong> {{ order.currency_sign }}{{ order.shipping }} </strong>
                </div>
                <div class="d-flex justify-content-between mb-3 text-danger">
                  <span>Discount</span>
                  <strong> -{{ order.currency_sign }}{{ order.discount_amount }} </strong>
                </div>
                <hr />
                <div class="d-flex justify-content-between">
                  <h5 class="fw-bold">Total</h5>
                  <h3 class="text-primary fw-bold">
                    {{ order.currency_sign }}{{ order.final_amount }}
                  </h3>
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
import { getImageUrl } from '../utils/ImageUrl'
import { formatDate } from '../utils/formatDate'
import { getOrderStatusBadgeClass } from '../utils/statusBadge'

export default {
  name: 'OrderDetail',
  data() {
    return {
      order: [],
      loading: false,
    }
  },
  mounted() {
    this.loadOrder()
  },
  methods: {
    async loadOrder() {
      this.loading = true
      try {
        const id = this.$route.params.id
        const response = await this.$axios.get(`/api/order-detail/${id}`)
        const data = response.data
        console.log(data)
        if (data.success) {
          this.order = data.data
        }
      } catch (error) {
        console.error('Error loading cart:', error)
      } finally {
        this.loading = false
      }
    },
    getImageUrl,
    formatDate,
    getOrderStatusBadgeClass,
  },
}
</script>
