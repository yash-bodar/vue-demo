<template>
  <div class="card card-vuexy p-4">
    <!-- Header -->
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-4 pb-3 border-bottom">
      <div class="d-flex align-items-center gap-3">
        <div class="badge bg-label-primary rounded-3 p-3">
          <i class="fas fa-receipt fs-3"></i>
        </div>
        <div>
          <h4 class="mb-0 fw-bold text-heading">Sales Orders</h4>
          <small class="text-muted">Total {{ orderCount }} orders processed</small>
        </div>
      </div>

      <div class="d-flex align-items-center gap-2">
        <button class="btn btn-sm btn-label-secondary" @click="exportData('pdf')" title="Export PDF">
          <i class="fas fa-file-pdf text-danger me-1"></i> PDF
        </button>
        <button class="btn btn-sm btn-label-secondary" @click="exportData('csv')" title="Export CSV">
          <i class="fas fa-file-csv text-success me-1"></i> CSV
        </button>
      </div>
    </div>

    <!-- Filter Bar -->
    <div class="row g-3 mb-4">
      <div class="col-12 col-md-4">
        <div class="input-group">
          <span class="input-group-text bg-transparent border-end-0 text-muted"><i class="fas fa-search"></i></span>
          <input type="search" class="form-control border-start-0 ps-0" v-model="filters.search" placeholder="Search customer or order ID..." />
        </div>
      </div>
      <div class="col-6 col-md-3">
        <select class="form-select" v-model="filters.status" @change="fetchOrders(1)">
          <option value="">All Order Status</option>
          <option value="completed">Completed</option>
          <option value="processing">Processing</option>
          <option value="pending">Pending</option>
          <option value="cancelled">Cancelled</option>
        </select>
      </div>
      <div class="col-6 col-md-3">
        <select class="form-select" v-model="filters.payment_status" @change="fetchOrders(1)">
          <option value="">All Payment Status</option>
          <option value="paid">Paid</option>
          <option value="pending">Pending</option>
          <option value="cancelled">Cancelled</option>
        </select>
      </div>
      <div class="col-12 col-md-2">
        <select class="form-select" v-model="filters.currency" @change="fetchOrders(1)">
          <option value="">All Currencies</option>
          <option value="USD">USD ($)</option>
          <option value="EUR">EUR (€)</option>
          <option value="GBP">GBP (£)</option>
        </select>
      </div>
    </div>

    <!-- Table -->
    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0">
        <thead>
          <tr>
            <th @click="sortByField('user_id', 'fetchOrders')" class="cursor-pointer">Customer</th>
            <th>Items</th>
            <th @click="sortByField('total_amount', 'fetchOrders')" class="cursor-pointer">Subtotal</th>
            <th @click="sortByField('final_amount', 'fetchOrders')" class="cursor-pointer">Final Total</th>
            <th @click="sortByField('status', 'fetchOrders')" class="cursor-pointer">Order Status</th>
            <th @click="sortByField('payment_status', 'fetchOrders')" class="cursor-pointer">Payment Status</th>
            <th @click="sortByField('created_at', 'fetchOrders')" class="cursor-pointer">Date</th>
            <th class="text-end">Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr v-if="ordersList.length > 0" v-for="order in ordersList" :key="order.id">
            <td>
              <div class="fw-bold text-heading fs-7">{{ order.user?.name || 'Guest User' }}</div>
              <small class="text-primary fs-8 fw-semibold">#{{ order.id }}</small>
            </td>
            <td>
              <span class="badge bg-label-primary fs-8">{{ order.items?.length || 0 }} Items</span>
            </td>
            <td class="fs-7">{{ order.currency_sign }} {{ order.total_amount }}</td>
            <td class="fw-bold text-primary fs-7">{{ order.currency_sign }} {{ order.final_amount }}</td>
            <td>
              <div class="position-relative">
                <span class="badge cursor-pointer" @click="toggleStatusDropdown(order.id)" :class="getOrderStatusBadgeClass(order.status)">
                  {{ order.status }} <i class="fas fa-chevron-down ms-1 fs-9"></i>
                </span>
                <div v-if="openStatusDropdown === order.id" class="position-absolute bg-card shadow-lg rounded-3 p-2 mt-1" style="z-index: 1000; min-width: 140px;">
                  <div v-for="st in statusOptions" :key="st.value" class="p-1 cursor-pointer rounded hover-bg-light fs-8" @click="updateOrderStatus(order.id, st.value)">
                    <span class="badge me-1" :class="getOrderStatusBadgeClass(st.value)">{{ st.value }}</span>
                  </div>
                </div>
              </div>
            </td>
            <td>
              <div class="position-relative">
                <span class="badge cursor-pointer" @click="togglePaymentStatusDropdown(order.id)" :class="getOrderStatusBadgeClass(order.payment_status)">
                  {{ order.payment_status }} <i class="fas fa-chevron-down ms-1 fs-9"></i>
                </span>
                <div v-if="openPaymentStatusDropdown === order.id" class="position-absolute bg-card shadow-lg rounded-3 p-2 mt-1" style="z-index: 1000; min-width: 140px;">
                  <div v-for="st in paymentStatusOptions" :key="st.value" class="p-1 cursor-pointer rounded hover-bg-light fs-8" @click="updatePaymentStatus(order.id, st.value)">
                    <span class="badge me-1" :class="getOrderStatusBadgeClass(st.value)">{{ st.value }}</span>
                  </div>
                </div>
              </div>
            </td>
            <td class="fs-8 text-muted">{{ formatDate(order.created_at) }}</td>
            <td class="text-end">
              <div class="d-inline-flex gap-1">
                <button class="btn btn-sm btn-icon btn-label-primary" @click="viewOrder(order.id)" title="View Details">
                  <i class="fas fa-eye fs-8"></i>
                </button>
                <button class="btn btn-sm btn-icon btn-label-secondary" @click="downloadInvoice(order.id)" title="Download Invoice">
                  <i class="fas fa-file-invoice fs-8"></i>
                </button>
              </div>
            </td>
          </tr>
          <tr v-else>
            <td colspan="8" class="text-center py-5 text-muted">
              <i class="fas fa-receipt fa-3x mb-3 opacity-50"></i>
              <h6>No orders found matching filters</h6>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Pagination -->
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 pt-4 border-top mt-3" v-if="lastPage > 1">
      <small class="text-muted fs-8">Showing {{ (currentPage - 1) * perPage + 1 }} to {{ Math.min(currentPage * perPage, orderCount) }} of {{ orderCount }} entries</small>
      <nav>
        <ul class="pagination mb-0 gap-1">
          <li class="page-item" :class="{ disabled: currentPage === 1 }">
            <button class="page-link rounded-circle border-0 text-primary" :disabled="currentPage === 1" @click="fetchOrders(currentPage - 1)">
              <i class="fas fa-chevron-left fs-8"></i>
            </button>
          </li>
          <li v-for="page in lastPage" :key="page" class="page-item" :class="{ active: page === currentPage }">
            <button class="page-link rounded-circle border-0 fw-bold" :class="page === currentPage ? 'bg-primary text-white' : 'text-primary'" @click="fetchOrders(page)">
              {{ page }}
            </button>
          </li>
          <li class="page-item" :class="{ disabled: currentPage === lastPage }">
            <button class="page-link rounded-circle border-0 text-primary" :disabled="currentPage === lastPage" @click="fetchOrders(currentPage + 1)">
              <i class="fas fa-chevron-right fs-8"></i>
            </button>
          </li>
        </ul>
      </nav>
    </div>
  </div>
</template>

<script>
import { sortByField, getSortIcon } from '../utils/table'
import { formatDate } from '../utils/formatDate'

export default {
  name: 'Orders',
  data() {
    return {
      ordersList: [],
      orderCount: 0,
      currentPage: 1,
      lastPage: 1,
      perPage: 10,
      filters: {
        status: '',
        payment_status: '',
        currency: '',
        search: ''
      },
      openStatusDropdown: null,
      openPaymentStatusDropdown: null,
      statusOptions: [
        { value: 'pending', label: 'Pending' },
        { value: 'processing', label: 'Processing' },
        { value: 'completed', label: 'Completed' },
        { value: 'cancelled', label: 'Cancelled' }
      ],
      paymentStatusOptions: [
        { value: 'pending', label: 'Pending' },
        { value: 'paid', label: 'Paid' },
        { value: 'cancelled', label: 'Cancelled' }
      ],
      searchTimeout: null
    }
  },
  watch: {
    'filters.search'() {
      if (this.searchTimeout) clearTimeout(this.searchTimeout)
      this.searchTimeout = setTimeout(() => this.fetchOrders(1), 300)
    }
  },
  mounted() {
    this.fetchOrders(1)
  },
  methods: {
    formatDate,
    sortByField,
    getSortIcon,
    fetchOrders(page = 1) {
      const params = new URLSearchParams({
        page: page,
        ...this.filters
      })
      this.$axios.get(`/api/orders?${params.toString()}`)
        .then(res => {
          this.ordersList = res.data.data.data
          this.orderCount = res.data.data.total
          this.currentPage = res.data.data.current_page
          this.lastPage = res.data.data.last_page
          this.perPage = res.data.data.per_page
        })
        .catch(err => console.error('Fetch orders error:', err))
    },
    toggleStatusDropdown(id) {
      this.openStatusDropdown = this.openStatusDropdown === id ? null : id
      this.openPaymentStatusDropdown = null
    },
    togglePaymentStatusDropdown(id) {
      this.openPaymentStatusDropdown = this.openPaymentStatusDropdown === id ? null : id
      this.openStatusDropdown = null
    },
    updateOrderStatus(orderId, newStatus) {
      this.$axios.post('/api/orders/update-status', { order_id: orderId, status: newStatus })
        .then(res => {
          if (res.data.success) {
            this.openStatusDropdown = null
            this.fetchOrders(this.currentPage)
          }
        })
        .catch(err => console.error('Update status error:', err))
    },
    updatePaymentStatus(orderId, newStatus) {
      this.$axios.post('/api/orders/update-payment-status', { order_id: orderId, payment_status: newStatus })
        .then(res => {
          if (res.data.success) {
            this.openPaymentStatusDropdown = null
            this.fetchOrders(this.currentPage)
          }
        })
        .catch(err => console.error('Update payment status error:', err))
    },
    getOrderStatusBadgeClass(status) {
      const mapping = {
        pending: 'bg-label-warning',
        paid: 'bg-label-success',
        completed: 'bg-label-success',
        processing: 'bg-label-info',
        cancelled: 'bg-label-danger'
      }
      return mapping[status?.toLowerCase()] || 'bg-label-secondary'
    },
    viewOrder(id) {
      this.$router.push(`/orders/detail/${id}`)
    },
    downloadInvoice(id) {
      window.open(`/api/orders/${id}/invoice`, '_blank')
    },
    exportData(type) {
      window.open(`/api/orders/export?type=${type}`, '_blank')
    }
  }
}
</script>