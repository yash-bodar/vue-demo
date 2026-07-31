<template>
  <div class="container-xl py-4">
    <!-- Header Section -->
    <div class="card card-vuexy p-4 mb-4">
      <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
        <div class="d-flex align-items-center gap-3">
          <div class="badge bg-label-primary rounded-3 p-3">
            <i class="fas fa-boxes-packing fs-3"></i>
          </div>
          <div>
            <h4 class="mb-0 fw-bold text-heading">My Purchase History</h4>
            <small class="text-muted">Track order status, line items & total breakdown</small>
          </div>
        </div>
        <span class="badge bg-label-primary fs-7">
          {{ orderCount ?? 0 }} Total Order{{ (orderCount ?? 0) !== 1 ? 's' : '' }}
        </span>
      </div>
    </div>

    <!-- Orders Body -->
    <div class="orders-body">
      <!-- Loading State -->
      <div v-if="loading" class="card card-vuexy p-5 text-center">
        <div class="spinner-border text-primary mx-auto mb-3" role="status"></div>
        <p class="text-muted m-0">Loading your purchase history...</p>
      </div>

      <!-- Empty State -->
      <div v-else-if="!orders || orders.length === 0" class="card card-vuexy p-5 text-center">
        <i class="fas fa-receipt fa-4x text-muted mb-3 opacity-50"></i>
        <h4 class="fw-bold text-heading">No Orders Placed Yet</h4>
        <p class="text-muted fs-7 mb-4">
          When you purchase items from our store, your order details will appear here.
        </p>
        <button
          class="btn btn-primary rounded-pill px-4 py-2 mx-auto"
          @click="$router.push('/product')"
        >
          <i class="fas fa-store me-2"></i>Explore Products
        </button>
      </div>

      <!-- Orders Accordion List -->
      <div v-else class="orders-list">
        <div class="accordion" id="ordersAccordion">
          <div v-for="(order, index) in orders" :key="index" class="card card-vuexy mb-3">
            <div class="accordion-item border-0">
              <h2 class="accordion-header">
                <button
                  class="accordion-button bg-card text-heading p-4 border-0 collapsed"
                  type="button"
                  data-bs-toggle="collapse"
                  :data-bs-target="'#collapse' + order.id"
                  aria-expanded="false"
                  :aria-controls="'collapse' + order.id"
                >
                  <div class="row align-items-center g-3 w-100 m-0">
                    <div class="col-6 col-md-3 p-0">
                      <small class="text-muted fs-8 text-uppercase fw-bold d-block"
                        >Order Reference</small
                      >
                      <span class="fw-bold text-primary fs-6">#{{ order.id }}</span>
                    </div>

                    <div class="col-6 col-md-3 p-0">
                      <small class="text-muted fs-8 text-uppercase fw-bold d-block"
                        >Date & Items</small
                      >
                      <div class="fs-7 fw-semibold">
                        <i class="far fa-calendar me-1"></i>{{ formatDate(order.created_at) }}
                      </div>
                      <small class="text-muted fs-8"
                        >{{ order.order_items?.length || order.items?.length || 0 }} Item(s)</small
                      >
                    </div>

                    <div class="col-6 col-md-3 p-0">
                      <small class="text-muted fs-8 text-uppercase fw-bold d-block">Status</small>
                      <div class="d-flex flex-wrap gap-1">
                        <span class="badge" :class="getOrderStatusBadgeClass(order.status)">
                          {{ order.status }}
                        </span>
                        <span class="badge" :class="getOrderStatusBadgeClass(order.payment_status)">
                          {{ order.payment_status }}
                        </span>
                      </div>
                    </div>

                    <div class="col-6 col-md-3 p-0 text-md-end text-start">
                      <small class="text-muted fs-8 text-uppercase fw-bold d-block"
                        >Total Amount</small
                      >
                      <span class="fw-bold text-heading fs-5">
                        {{ user?.currency_sign || '$'
                        }}{{ order.final_amount || order.total_amount }}
                      </span>
                    </div>
                  </div>
                </button>
              </h2>

              <!-- Order Items Dropdown -->
              <div
                :id="'collapse' + order.id"
                class="accordion-collapse collapse"
                data-bs-parent="#ordersAccordion"
              >
                <div class="card-body p-4 bg-light border-top">
                  <div
                    v-for="item in order.order_items || order.items || []"
                    :key="item.id"
                    class="p-3 mb-2 rounded-3 bg-card border border-subtle"
                  >
                    <div class="row align-items-center g-3">
                      <div class="col-3 col-sm-2 col-md-1">
                        <img
                          :src="getImageUrl(item.product?.image)"
                          class="rounded-3 img-fluid border product-thumb-sq"
                          :alt="item.product?.name"
                        />
                      </div>
                      <div
                        class="col-9 col-sm-10 col-md-7 cursor-pointer"
                        @click="$router.push(`/product/detail/${item.product_id}`)"
                      >
                        <h6 class="fw-bold text-heading mb-1">
                          {{ item.product?.name }}
                          <span v-if="item.variant" class="badge bg-label-primary fs-9 ms-1">
                            {{ item.variant.name }}
                          </span>
                        </h6>
                        <small class="text-muted fs-8">
                          {{ user?.currency_sign || '$' }}{{ item.price }} x {{ item.quantity }}
                        </small>
                      </div>
                      <div class="col-12 col-md-4 text-md-end text-start">
                        <span class="fw-bold text-heading fs-6">
                          {{ user?.currency_sign || '$'
                          }}{{ (item.price * item.quantity).toFixed(2) }}
                        </span>
                      </div>
                    </div>
                  </div>

                  <!-- Summary Table -->
                  <div class="row justify-content-end mt-4">
                    <div class="col-md-5 col-lg-4">
                      <div class="p-3 rounded-3 bg-card border border-subtle fs-7">
                        <div class="d-flex justify-content-between mb-1">
                          <span class="text-muted">SubTotal</span>
                          <span class="fw-bold"
                            >{{ user?.currency_sign || '$' }}{{ order.total_amount }}</span
                          >
                        </div>
                        <div class="d-flex justify-content-between mb-1">
                          <span class="text-muted">Shipping</span>
                          <span class="fw-bold"
                            >{{ user?.currency_sign || '$' }}{{ order.shipping || 0 }}</span
                          >
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                          <span class="text-muted">Discount</span>
                          <span class="fw-bold text-danger"
                            >-{{ user?.currency_sign || '$' }}{{ order.discount_amount || 0 }}</span
                          >
                        </div>
                        <div
                          class="d-flex justify-content-between align-items-center pt-2 border-top"
                        >
                          <span class="fw-bold text-heading">Grand Total</span>
                          <span class="fw-bold text-primary fs-5"
                            >{{ user?.currency_sign || '$'
                            }}{{ order.final_amount || order.total_amount }}</span
                          >
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Pagination -->
        <div class="d-flex justify-content-center mt-4" v-if="lastPage > 1">
          <div class="card card-vuexy p-3 px-4">
            <nav>
              <ul class="pagination mb-0 gap-2">
                <li class="page-item" :class="{ disabled: currentPage === 1 }">
                  <button
                    class="page-link rounded-circle border-0 text-primary"
                    :disabled="currentPage === 1"
                    @click="loadOrders(currentPage - 1)"
                  >
                    <i class="fas fa-chevron-left"></i>
                  </button>
                </li>
                <li
                  v-for="page in lastPage"
                  :key="page"
                  class="page-item"
                  :class="{ active: page === currentPage }"
                >
                  <button
                    class="page-link rounded-circle border-0 fw-bold"
                    :class="page === currentPage ? 'bg-primary text-white' : 'text-primary'"
                    @click="loadOrders(page)"
                  >
                    {{ page }}
                  </button>
                </li>
                <li class="page-item" :class="{ disabled: currentPage === lastPage }">
                  <button
                    class="page-link rounded-circle border-0 text-primary"
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
</template>

<script>
import { mapState } from 'pinia'
import { useAuthStore } from '../stores/auth'
import { getImageUrl } from '../utils/ImageUrl'

export default {
  name: 'MyOrders',
  data() {
    return {
      orders: [],
      loading: false,
      orderCount: 0,
      currentPage: 1,
      perPage: 5,
      lastPage: 1,
    }
  },
  computed: {
    ...mapState(useAuthStore, ['user']),
  },
  mounted() {
    this.loadOrders(1)
  },
  methods: {
    getImageUrl,
    async loadOrders(page = 1) {
      this.loading = true
      try {
        const response = await this.$axios.get(`/api/get-orders?page=${page}`)
        const data = response.data
        if (data.success) {
          this.orders = data.data.data
          this.orderCount = data.data.total
          this.currentPage = data.data.current_page
          this.lastPage = data.data.last_page
          this.perPage = data.data.per_page
        }
      } catch (error) {
        console.error('Error loading orders:', error)
      } finally {
        this.loading = false
      }
    },
    formatDate(dateString) {
      if (!dateString) return 'N/A'
      return new Date(dateString).toLocaleDateString(undefined, {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
      })
    },
    getOrderStatusBadgeClass(status) {
      const mapping = {
        pending: 'bg-label-warning',
        paid: 'bg-label-success',
        completed: 'bg-label-success',
        processing: 'bg-label-info',
        failed: 'bg-label-danger',
        cancelled: 'bg-label-danger',
      }
      return mapping[status?.toLowerCase()] || 'bg-label-secondary'
    },
  },
}
</script>
