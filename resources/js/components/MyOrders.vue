<template>
  <div class="orders-container bg-light">
    <!-- Orders Header -->
    <div class="cart-header bg-white m-2 border-bottom shadow-sm p-3 mx-0 mx-md-4 my-3 rounded-2">
      <div class="row align-items-center g-3">
        <div class="col-12 col-md-6">
          <div class="d-flex align-items-center">
            <div class="me-3"><i class="fas fa-box fa-2x text-primary"></i></div>
            <div>
              <h5 class="mb-0 fw-bold text-dark">My Orders</h5>
              <p class="mb-0 text-muted small">
                {{ orderCount ?? 0 }} order{{ (orderCount ?? 0) !== 1 ? 's' : '' }}
              </p>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Orders Body -->
    <div class="orders-body p-3 p-md-4">

      <!-- Loading State -->
      <div v-if="loading" class="text-center py-5">
        <div class="spinner-border text-primary" role="status">
          <span class="visually-hidden">Loading...</span>
        </div>
        <p class="mt-3 text-muted">Loading your orders...</p>
      </div>

      <!-- Empty State -->
      <div v-else-if="!orders || (orders.length === 0)" class="text-center py-5">
        <div class="empty-state-icon"><i class="fas fa-box-open fa-5x text-muted mb-3"></i></div>
        <h5 class="text-muted">No orders found</h5>
        <p class="text-muted mb-4">You haven't placed any orders yet.</p>
        <button class="btn btn-primary bg-primary-linear" @click="$router.push('/product')"><i
            class="fas fa-shopping-bag me-2"></i>Start Shopping</button>
      </div>

      <!-- Orders List -->
      <div v-else class="orders-list">
        <div class="accordion" :id="'ordersAccordion'">
          <div v-for="(order, index) in orders" :key="index" class="accordion-item mb-3 shadow-sm rounded-2">
            <!-- Order Header -->
            <h2 class="accordion-header">
              <button class="accordion-button bg-primary text-light fw-bold rounded-2 collapsed" type="button"
                data-bs-toggle="collapse" :data-bs-target="'#collapse' + order.id" aria-expanded="false"
                :aria-controls="'collapse' + order.id">
                <div class="row w-100 align-items-center">
                  <div class="col-md-6">
                    <div class="d-flex align-items-center gap-3">
                      <div>
                        <h6 class="fw-bold text-light mb-0">Order #{{ (index + 1) * currentPage }} ({{ order.items.length }} item{{ order.items.length !== 1 ? 's' : ''}})</h6>
                        <small class="text-light">{{ formatDate(order.created_at) }}</small>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-6 text-md-end pe-3">
                    <span class="badge fw-bold rounded-3 px-3 py-2" :class="getOrderStatusBadgeClass(order.status)">{{
                      order.status }}</span>
                    <span class="badge fw-bold rounded-3 px-3 py-2 ms-2"
                      :class="getOrderStatusBadgeClass(order.payment_status)">{{ order.payment_status }}</span>
                  </div>
                </div>
              </button>
            </h2>

            <!-- Order Items -->
            <div :id="'collapse' + order.id" class="accordion-collapse collapse" :data-bs-parent="'#ordersAccordion'">
              <div class="accordion-body p-3 p-md-4">
                <div v-for="item in order.items" :key="item.product_id" class="order-item mb-3 pb-3 border-bottom">
                  <div class="row align-items-center">
                    <div class="col-4 col-sm-3 col-md-2">
                      <img :src="getImageUrl(item.product_image)" class="img-fluid rounded order-item-image"
                        :alt="item.product_name">
                    </div>
                    <div class="col-8 col-sm-9 col-md-6 cursor-pointer" @click="$router.push(`/product/detail/${item.product_id}`)">
                      <h6 class="fw-semibold mb-1">{{ item.product_name }}</h6>
                      <p class="text-muted small mb-0">
                        <span class="fw-bold">{{ user?.currency_sign || '$' }}{{ item.price }}</span><span
                          class="ms-2">x {{ item.quantity }}</span>
                      </p>
                    </div>
                    <div class="col-12 col-md-4 text-end mt-2 mt-md-0">
                      <span class="fw-bold text-primary fs-5 d-inline-block">{{ user?.currency_sign || '$' }}{{ item.total }}</span>
                    </div>
                  </div>
                </div>

                <!-- Order Total -->
                <div class="order-total mt-4 p-3 border-top bg-light rounded-2">
                    <div class="text-end">
                      <span class="text-muted me-2">SubTotal: </span><span class="fw-bold text-primary fs-5">{{user?.currency_sign || '$' }}{{ order.total_amount }}</span>
                    </div>
                    <div class="text-end">
                      <span class="text-muted me-2">Shipping:</span><span class="fw-bold text-primary fs-5">{{user?.currency_sign || '$' }}{{ order.shipping }}</span>
                    </div>
                    <div class="text-end">
                      <span class="text-muted me-2">Discount:</span><span class="fw-bold text-danger fs-5">-{{user?.currency_sign || '$' }}{{ order.discount_amount }}</span>
                    </div>
                    <div class="text-end">
                      <span class="text-muted me-2 fw-bold">Total:</span><span class="fw-bold text-primary fs-4">{{user?.currency_sign || '$' }}{{ order.final_amount }}</span>
                    </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="card-footer bg-white border-0 p-4" v-if="lastPage > 1">
          <div class="pagination-wrapper">
            <div class="pagination-info text-center mb-3">
              <span class="text-muted">Showing <strong>{{ (currentPage - 1) * perPage + 1 }}</strong> to <strong>{{
                Math.min(currentPage * perPage, orderCount) }}</strong> of <strong>{{ orderCount }}</strong>
                Orders</span>
            </div>
            <nav aria-label="Orders pagination">
              <ul class="pagination justify-content-center mb-0 gap-2">
                <li class="page-item" :class="{ disabled: currentPage === 1 }">
                  <button class="page-link rounded-3 color-primary" style="min-width: 42px; height: 42px;"
                    :disabled="currentPage === 1" @click="loadOrders(currentPage - 1)">
                    <i class="fas fa-chevron-left"></i>
                  </button>
                </li>
                <li v-for="page in lastPage" :key="page" class="page-item" :class="{ active: page === currentPage }">
                  <button class="page-link rounded-3" style="min-width: 42px; height: 42px;"
                    :class="page === currentPage ? 'bg-primary text-white' : 'color-primary'" @click="loadOrders(page)">
                    {{ page }}
                  </button>
                </li>
                <li class="page-item" :class="{ disabled: currentPage === lastPage }">
                  <button class="page-link rounded-3 color-primary" style="min-width: 42px; height: 42px;"
                    :disabled="currentPage === lastPage" @click="loadOrders(currentPage + 1)">
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
        // console.log(data);
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