<template>
  <div class="container py-5">
    <!-- Header Section -->
    <div class="card card-premium mb-4 p-4">
      <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
        <div class="d-flex align-items-center">
          <div class="me-3 d-inline-flex align-items-center justify-content-center rounded-3 bg-primary text-white" style="width: 48px; height: 48px;">
            <i class="fas fa-box fs-4"></i>
          </div>
          <div>
            <h5 class="mb-0 fw-bold text-dark">My Purchase Orders</h5>
            <p class="mb-0 text-muted small">
              Track status, details, and invoice breakdowns for all orders
            </p>
          </div>
        </div>
        <span class="badge badge-primary-soft px-3 py-2 rounded-pill fw-bold">
          {{ orderCount ?? 0 }} Total Order{{ (orderCount ?? 0) !== 1 ? 's' : '' }}
        </span>
      </div>
    </div>

    <!-- Orders Body -->
    <div class="orders-body">
      <!-- Loading State -->
      <div v-if="loading" class="text-center py-5">
        <div class="spinner-border text-primary" role="status">
          <span class="visually-hidden">Loading...</span>
        </div>
        <p class="mt-3 text-muted">Loading your purchase history...</p>
      </div>

      <!-- Empty State -->
      <div v-else-if="!orders || (orders.length === 0)" class="text-center py-5 bg-white rounded-4 shadow-sm border border-light">
        <i class="fas fa-receipt fa-4x text-muted mb-3 opacity-60"></i>
        <h5 class="text-muted fw-bold">No orders found</h5>
        <p class="text-muted small mb-4">You haven't placed any orders yet. Start exploring our shop!</p>
        <button class="btn btn-primary px-4 py-2" @click="$router.push('/product')">
          <i class="fas fa-shopping-bag me-2"></i>Browse Products
        </button>
      </div>

      <!-- Orders List -->
      <div v-else class="orders-list">
        <div class="accordion" id="ordersAccordion">
          <div v-for="(order, index) in orders" :key="index" class="card card-premium mb-3 border-0">
            <!-- Order Header inside Accordion Button -->
            <div class="accordion-item border-0">
              <h2 class="accordion-header">
                <button 
                  class="accordion-button bg-white text-dark d-block p-4 border-0 collapsed" 
                  type="button"
                  data-bs-toggle="collapse" 
                  :data-bs-target="'#collapse' + order.id" 
                  aria-expanded="false"
                  :aria-controls="'collapse' + order.id"
                  style="border-radius: 1.25rem !important;"
                >
                  <div class="row align-items-center g-3 w-100 m-0">
                    <!-- Column 1: Order Reference -->
                    <div class="col-6 col-md-3 p-0">
                      <span class="text-muted small d-block text-uppercase fw-bold mb-1" style="font-size: 0.7rem; letter-spacing: 0.05em;">Order Reference</span>
                      <span class="fw-extrabold text-dark fs-6">#{{ order.id }}</span>
                    </div>

                    <!-- Column 2: Date & Items -->
                    <div class="col-6 col-md-3 p-0">
                      <span class="text-muted small d-block text-uppercase fw-bold mb-1" style="font-size: 0.7rem; letter-spacing: 0.05em;">Date & Items</span>
                      <div class="text-dark small fw-semibold">
                        <i class="far fa-calendar-alt text-muted me-1"></i>{{ formatDate(order.created_at) }}
                      </div>
                      <small class="text-muted"><i class="fas fa-boxes text-muted me-1"></i>{{ order.order_items?.length || 0 }} Item(s)</small>
                    </div>

                    <!-- Column 3: Status -->
                    <div class="col-6 col-md-3 p-0">
                      <span class="text-muted small d-block text-uppercase fw-bold mb-1" style="font-size: 0.7rem; letter-spacing: 0.05em;">Status</span>
                      <div class="d-flex flex-wrap gap-1">
                        <span class="badge fw-bold px-2 py-1 text-capitalize" :class="getOrderStatusBadgeClass(order.status)" style="font-size: 0.75rem;">
                          {{ order.status }}
                        </span>
                        <span class="badge fw-bold px-2 py-1 text-capitalize" :class="getOrderStatusBadgeClass(order.payment_status)" style="font-size: 0.75rem;">
                          {{ order.payment_status }}
                        </span>
                      </div>
                    </div>

                    <!-- Column 4: Price -->
                    <div class="col-6 col-md-3 p-0 text-md-end text-start">
                      <span class="text-muted small d-block text-uppercase fw-bold mb-1" style="font-size: 0.7rem; letter-spacing: 0.05em;">Total Amount</span>
                      <span class="fw-extrabold text-primary fs-5">
                        {{ user?.currency_sign || '$' }}{{ order.final_amount }}
                      </span>
                    </div>
                  </div>
                </button>
              </h2>

              <!-- Order Items Dropdown -->
              <div :id="'collapse' + order.id" class="accordion-collapse collapse" data-bs-parent="#ordersAccordion">
                <div class="card-body p-4 bg-light bg-opacity-50 border-top border-light">
                  <div v-for="item in order.order_items" :key="item.id" class="mb-3 pb-3 border-bottom border-light">
                    <div class="row align-items-center g-3">
                      <!-- Product Image -->
                      <div class="col-3 col-sm-2 col-md-1">
                        <div class="premium-product-img-wrapper" style="border-radius: 0.5rem;">
                          <img :src="getImageUrl(item.product?.image)" class="premium-product-img" :alt="item.product?.name">
                        </div>
                      </div>
                      <!-- Product Description -->
                      <div class="col-9 col-sm-10 col-md-7 cursor-pointer" @click="$router.push(`/product/detail/${item.product_id}`)">
                        <h6 class="fw-bold mb-1">
                          {{ item.product?.name }}
                          <span v-if="item.variant" class="badge bg-secondary-soft text-muted small ms-2" style="font-size: 0.65rem;">
                            {{ item.variant.name }}
                          </span>
                        </h6>
                        <span class="text-muted small">
                          {{ user?.currency_sign || '$' }}{{ item.price }} x {{ item.quantity }}
                        </span>
                      </div>
                      <!-- Subtotal -->
                      <div class="col-12 col-md-4 text-md-end text-start">
                        <span class="text-muted small d-block d-md-none">Subtotal</span>
                        <span class="fw-bold text-dark fs-5">
                          {{ user?.currency_sign || '$' }}{{ (item.price * item.quantity).toFixed(2) }}
                        </span>
                      </div>
                    </div>
                  </div>

                  <!-- Summary Table -->
                  <div class="row justify-content-end mt-4">
                    <div class="col-md-5 col-lg-4">
                      <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted small">SubTotal</span>
                        <span class="fw-semibold text-dark">{{ user?.currency_sign || '$' }}{{ order.total_amount }}</span>
                      </div>
                      <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted small">Shipping</span>
                        <span class="fw-semibold text-dark">{{ user?.currency_sign || '$' }}{{ order.shipping }}</span>
                      </div>
                      <div class="d-flex justify-content-between mb-3">
                        <span class="text-muted small">Discount</span>
                        <span class="fw-semibold text-danger">-{{ user?.currency_sign || '$' }}{{ order.discount_amount }}</span>
                      </div>
                      <hr class="border-light">
                      <div class="d-flex justify-content-between align-items-baseline">
                        <span class="fw-bold text-dark">Grand Total</span>
                        <span class="fw-extrabold text-primary fs-4">
                          {{ user?.currency_sign || '$' }}{{ order.final_amount }}
                        </span>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Pagination Section -->
        <div class="mt-5 d-flex justify-content-center" v-if="lastPage > 1">
          <div class="card card-premium p-3 px-4">
            <div class="d-flex flex-column align-items-center gap-2">
              <span class="text-muted small">
                Showing <strong>{{ (currentPage - 1) * perPage + 1 }}</strong> to <strong>{{ Math.min(currentPage * perPage, orderCount) }}</strong> of <strong>{{ orderCount }}</strong> orders
              </span>
              <nav aria-label="Orders pagination">
                <ul class="pagination mb-0 gap-2">
                  <li class="page-item" :class="{ disabled: currentPage === 1 }">
                    <button 
                      class="page-link rounded-circle border-0 d-flex align-items-center justify-content-center" 
                      style="width: 36px; height: 36px; color: var(--primary-color);"
                      :disabled="currentPage === 1" 
                      @click="loadOrders(currentPage - 1)"
                    >
                      <i class="fas fa-chevron-left"></i>
                    </button>
                  </li>
                  <li v-for="page in lastPage" :key="page" class="page-item" :class="{ active: page === currentPage }">
                    <button 
                      class="page-link rounded-circle border-0 d-flex align-items-center justify-content-center" 
                      style="width: 36px; height: 36px; font-weight: 600;"
                      :class="page === currentPage ? 'bg-primary text-white' : 'color-primary'" 
                      @click="loadOrders(page)"
                    >
                      {{ page }}
                    </button>
                  </li>
                  <li class="page-item" :class="{ disabled: currentPage === lastPage }">
                    <button 
                      class="page-link rounded-circle border-0 d-flex align-items-center justify-content-center" 
                      style="width: 36px; height: 36px; color: var(--primary-color);"
                      :disabled="currentPage === lastPage" 
                      @click="loadOrders(currentPage + 1)"
                    >
                      <i class="fas fa-chevron-right"></i>
                    </button>
                  </li>
                </ul>
              </nav>
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
import { getImageUrl } from '../utils/ImageUrl'
import { formatDate } from '../utils/formatDate'
import { getOrderStatusBadgeClass } from '../utils/statusBadge'

export default {
  name: 'MyOrders',
  computed: {
    ...mapState(useAuthStore, ['user']),
  },
  data() {
    return {
      orders: [],
      loading: false,
      orderCount: 0,
      currentPage: 1,
      perPage: 10,
      lastPage: 1,
      filters: {}
    }
  },
  mounted() {
    this.loadOrders();
  },
  methods: {
    async loadOrders(page = 1) {
      this.loading = true;
      try {
        const params = new URLSearchParams({
          page: page,
          user_id: this.user.id,
          ...this.filters
        });
        const response = await this.$axios.get(`/api/get-orders?${params.toString()}`);
        const data = response.data;
        if (data.success) {
          this.orders = data.data.data;
          this.orderCount = data.data.total;
          this.currentPage = data.data.current_page;
          this.perPage = data.data.per_page;
          this.lastPage = data.data.last_page;
        }
      } catch (error) {
        console.error('Error loading cart:', error);
      } finally {
        this.loading = false;
      }
    },
    getImageUrl,
    formatDate,
    getOrderStatusBadgeClass
  }
}
</script>