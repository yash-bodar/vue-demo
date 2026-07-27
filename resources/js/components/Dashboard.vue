<template>
  <div class="admin-dashboard">
    <!-- Header & Welcome Banner -->
    <div class="card card-vuexy bg-primary text-white p-4 mb-4 border-0 position-relative overflow-hidden shadow-primary">
      <div class="row align-items-center position-relative" style="z-index: 2;">
        <div class="col-md-8">
          <h3 class="fw-bold mb-1 text-white">Welcome back, {{ user?.name || 'Admin' }}! 👋</h3>
          <p class="mb-0 text-white-50 fs-6">Here is what is happening with your eCommerce store performance today.</p>
        </div>
        <div class="col-md-4 text-md-end mt-3 mt-md-0">
          <router-link to="/products/create" class="btn btn-light text-primary fw-bold shadow-sm rounded-pill">
            <i class="fas fa-plus me-1"></i> Add New Product
          </router-link>
        </div>
      </div>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="row g-4 mb-4">
      <div v-for="i in 4" :key="i" class="col-md-3">
        <div class="card card-vuexy p-3">
          <div class="skeleton-box py-4 mb-2"></div>
          <div class="skeleton-box py-2 w-50"></div>
        </div>
      </div>
    </div>

    <!-- Error Alert -->
    <div v-else-if="error" class="alert alert-danger shadow-sm rounded-3">{{ error }}</div>

    <div v-else>
      <!-- Statistic Cards (Primary 4 Metrics) -->
      <div class="row g-4 mb-4">
        <!-- Total Users Card -->
        <div class="col-xl-3 col-sm-6">
          <div class="card card-vuexy p-3 h-100">
            <div class="d-flex align-items-center justify-content-between mb-3">
              <div>
                <span class="fs-8 fw-semibold text-muted text-uppercase">Total Customers</span>
                <h3 class="fw-bold m-0 mt-1">{{ stats.total_users || 0 }}</h3>
              </div>
              <div class="badge bg-label-primary rounded-3 p-3">
                <i class="fas fa-users fs-4"></i>
              </div>
            </div>
            <div class="d-flex align-items-center gap-1 fs-8 text-success">
              <i class="fas fa-arrow-up"></i>
              <span class="fw-bold">+14.2%</span>
              <span class="text-muted ms-1">than last month</span>
            </div>
          </div>
        </div>

        <!-- Total Products Card -->
        <div class="col-xl-3 col-sm-6">
          <div class="card card-vuexy p-3 h-100">
            <div class="d-flex align-items-center justify-content-between mb-3">
              <div>
                <span class="fs-8 fw-semibold text-muted text-uppercase">Total Products</span>
                <h3 class="fw-bold m-0 mt-1">{{ stats.total_products || 0 }}</h3>
              </div>
              <div class="badge bg-label-success rounded-3 p-3">
                <i class="fas fa-boxes-stacked fs-4"></i>
              </div>
            </div>
            <div class="d-flex align-items-center gap-1 fs-8 text-success">
              <i class="fas fa-check-circle"></i>
              <span class="fw-bold">{{ stats.active_products || 0 }} Active</span>
              <span class="text-muted ms-1">in store catalog</span>
            </div>
          </div>
        </div>

        <!-- Total Orders Card -->
        <div class="col-xl-3 col-sm-6">
          <div class="card card-vuexy p-3 h-100">
            <div class="d-flex align-items-center justify-content-between mb-3">
              <div>
                <span class="fs-8 fw-semibold text-muted text-uppercase">Total Orders</span>
                <h3 class="fw-bold m-0 mt-1">{{ stats.total_orders || 0 }}</h3>
              </div>
              <div class="badge bg-label-info rounded-3 p-3">
                <i class="fas fa-shopping-cart fs-4"></i>
              </div>
            </div>
            <div class="d-flex align-items-center gap-1 fs-8 text-warning">
              <i class="fas fa-clock"></i>
              <span class="fw-bold">{{ stats.pending_orders || 0 }} Pending</span>
              <span class="text-muted ms-1">orders awaiting action</span>
            </div>
          </div>
        </div>

        <!-- Total Revenue Card -->
        <div class="col-xl-3 col-sm-6">
          <div class="card card-vuexy p-3 h-100">
            <div class="d-flex align-items-center justify-content-between mb-3">
              <div>
                <span class="fs-8 fw-semibold text-muted text-uppercase">Total Revenue</span>
                <h3 class="fw-bold m-0 mt-1 text-primary">{{ formatCurrency(stats.total_revenue) }}</h3>
              </div>
              <div class="badge bg-label-warning rounded-3 p-3">
                <i class="fas fa-wallet fs-4"></i>
              </div>
            </div>
            <div class="d-flex align-items-center gap-1 fs-8 text-success">
              <i class="fas fa-arrow-up"></i>
              <span class="fw-bold">+28.4%</span>
              <span class="text-muted ms-1">gross sales growth</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Quick Action Shortcuts -->
      <div class="row g-3 mb-4">
        <div class="col-md-3 col-6">
          <router-link to="/products/create" class="card card-vuexy p-3 text-center text-decoration-none text-heading h-100">
            <div class="badge bg-label-primary rounded-circle p-3 mx-auto mb-2" style="width: 48px; height: 48px;">
              <i class="fas fa-plus fs-5"></i>
            </div>
            <div class="fw-bold fs-7">Add Product</div>
          </router-link>
        </div>
        <div class="col-md-3 col-6">
          <router-link to="/orders" class="card card-vuexy p-3 text-center text-decoration-none text-heading h-100">
            <div class="badge bg-label-success rounded-circle p-3 mx-auto mb-2" style="width: 48px; height: 48px;">
              <i class="fas fa-receipt fs-5"></i>
            </div>
            <div class="fw-bold fs-7">Process Orders</div>
          </router-link>
        </div>
        <div class="col-md-3 col-6">
          <router-link to="/coupons/create" class="card card-vuexy p-3 text-center text-decoration-none text-heading h-100">
            <div class="badge bg-label-warning rounded-circle p-3 mx-auto mb-2" style="width: 48px; height: 48px;">
              <i class="fas fa-ticket fs-5"></i>
            </div>
            <div class="fw-bold fs-7">Create Coupon</div>
          </router-link>
        </div>
        <div class="col-md-3 col-6">
          <router-link to="/categories/create" class="card card-vuexy p-3 text-center text-decoration-none text-heading h-100">
            <div class="badge bg-label-info rounded-circle p-3 mx-auto mb-2" style="width: 48px; height: 48px;">
              <i class="fas fa-folder-plus fs-5"></i>
            </div>
            <div class="fw-bold fs-7">Add Category</div>
          </router-link>
        </div>
      </div>

      <!-- Canvas Charts Section -->
      <div class="row g-4 mb-4">
        <!-- Monthly Revenue Chart -->
        <div class="col-lg-6">
          <div class="card card-vuexy p-4 h-100">
            <div class="d-flex align-items-center justify-content-between mb-3">
              <div>
                <h5 class="fw-bold m-0">Monthly Revenue Overview</h5>
                <small class="text-muted">Total income generated per month</small>
              </div>
              <span class="badge bg-label-primary">Year {{ new Date().getFullYear() }}</span>
            </div>
            <div class="chart-container" style="position: relative; height: 260px;">
              <canvas ref="revenueChart"></canvas>
            </div>
          </div>
        </div>

        <!-- Monthly Orders Chart -->
        <div class="col-lg-6">
          <div class="card card-vuexy p-4 h-100">
            <div class="d-flex align-items-center justify-content-between mb-3">
              <div>
                <h5 class="fw-bold m-0">Monthly Sales Volume</h5>
                <small class="text-muted">Completed vs pending order count</small>
              </div>
              <span class="badge bg-label-success">Orders Growth</span>
            </div>
            <div class="chart-container" style="position: relative; height: 260px;">
              <canvas ref="ordersChart"></canvas>
            </div>
          </div>
        </div>
      </div>

      <!-- Recent Orders & Top Products Lists -->
      <div class="row g-4 mb-4">
        <!-- Recent Orders Table -->
        <div class="col-lg-8">
          <div class="card card-vuexy p-4 h-100">
            <div class="d-flex align-items-center justify-content-between mb-3">
              <h5 class="fw-bold m-0">Recent Customer Orders</h5>
              <router-link to="/orders" class="btn btn-sm btn-label-primary">View All Orders</router-link>
            </div>

            <div class="table-responsive">
              <table class="table table-hover align-middle m-0 fs-7">
                <thead>
                  <tr>
                    <th>Order ID</th>
                    <th>Customer</th>
                    <th>Date</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th class="text-end">Action</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-if="recentOrders.length === 0">
                    <td colspan="6" class="text-center py-4 text-muted">No recent orders found.</td>
                  </tr>
                  <tr v-for="order in recentOrders" :key="order.id">
                    <td class="fw-bold text-primary">#{{ order.id }}</td>
                    <td>
                      <div class="fw-semibold">{{ order.user_name || 'Guest User' }}</div>
                    </td>
                    <td class="text-muted fs-8">{{ formatDate(order.created_at) }}</td>
                    <td class="fw-bold">{{ formatCurrency(order.total_amount, order.currency) }}</td>
                    <td>
                      <span :class="getStatusBadgeClass(order.status)" class="badge">
                        {{ order.status }}
                      </span>
                    </td>
                    <td class="text-end">
                      <router-link :to="`/orders/detail/${order.id}`" class="btn btn-sm btn-icon btn-label-secondary">
                        <i class="fas fa-eye"></i>
                      </router-link>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>

        <!-- Activity & Latest Products -->
        <div class="col-lg-4">
          <div class="card card-vuexy p-4 h-100">
            <div class="d-flex align-items-center justify-content-between mb-3">
              <h5 class="fw-bold m-0">Latest Catalog Additions</h5>
              <router-link to="/products" class="btn btn-sm btn-link p-0 text-primary fs-7">Catalog</router-link>
            </div>

            <div class="d-flex flex-column gap-3">
              <div v-if="topProducts.length === 0" class="text-muted py-3">No products available</div>
              <div v-for="prod in topProducts.slice(0, 5)" :key="prod.id" class="d-flex align-items-center justify-content-between p-2 rounded-3 border-subtle border">
                <div class="d-flex align-items-center gap-3">
                  <div class="badge bg-label-primary rounded-3 p-2">
                    <i class="fas fa-box fs-5"></i>
                  </div>
                  <div>
                    <div class="fw-bold fs-7 text-truncate" style="max-width: 140px;">{{ prod.name }}</div>
                    <div class="fs-8 text-muted">{{ prod.category || 'General' }}</div>
                  </div>
                </div>
                <div class="text-end">
                  <div class="fw-bold text-primary fs-7">{{ formatCurrency(prod.price, prod.currency) }}</div>
                  <span class="fs-8 badge" :class="prod.status === 'active' ? 'bg-label-success' : 'bg-label-secondary'">
                    {{ prod.status }}
                  </span>
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
import { mapState } from 'pinia'
import { useAuthStore } from '../stores/auth'

export default {
  name: 'Dashboard',
  data() {
    return {
      loading: false,
      error: null,
      stats: {},
      recentOrders: [],
      topProducts: [],
      charts: {
        monthly_revenue: {},
        monthly_orders: {}
      }
    }
  },
  computed: {
    ...mapState(useAuthStore, ['user'])
  },
  async mounted() {
    await this.fetchDashboardData()
    this.initCharts()
  },
  methods: {
    async fetchDashboardData() {
      this.loading = true
      try {
        const response = await this.$axios.get('/api/get-dashboard-data')
        const data = response.data

        this.stats = data.stats || {}
        this.recentOrders = data.recent_orders || []
        this.topProducts = data.top_products || []
        this.charts = data.charts || {}
      } catch (err) {
        this.error = 'Failed to load dashboard statistics'
        console.error('Dashboard API Error:', err)
      } finally {
        this.loading = false
      }
    },
    formatCurrency(amount, currency = 'USD') {
      const signs = { 'USD': '$', 'EUR': '€', 'GBP': '£', 'INR': '₹' }
      const sign = signs[currency] || '$'
      return `${sign}${parseFloat(amount || 0).toFixed(2)}`
    },
    formatDate(dateStr) {
      if (!dateStr) return 'N/A'
      return new Date(dateStr).toLocaleDateString(undefined, { month: 'short', day: 'numeric', year: 'numeric' })
    },
    getStatusBadgeClass(status) {
      const mapping = {
        pending: 'bg-label-warning',
        completed: 'bg-label-success',
        processing: 'bg-label-info',
        cancelled: 'bg-label-danger'
      }
      return mapping[status] || 'bg-label-secondary'
    },
    initCharts() {
      this.$nextTick(() => {
        this.createRevenueChart()
        this.createOrdersChart()
      })
    },
    createRevenueChart() {
      const canvas = this.$refs.revenueChart
      if (!canvas) return
      const ctx = canvas.getContext('2d')

      // Set resolution for high DPI screens
      const width = canvas.width = canvas.parentElement.clientWidth || 400
      const height = canvas.height = 240

      const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec']
      const data = months.map((_, i) => this.charts.monthly_revenue[i + 1] || 0)
      const maxVal = Math.max(...data, 100)

      ctx.clearRect(0, 0, width, height)

      // Draw Grid Lines
      ctx.strokeStyle = '#e6e6e8'
      ctx.lineWidth = 0.5
      for (let i = 0; i <= 4; i++) {
        const y = (height - 40) / 4 * i + 10
        ctx.beginPath()
        ctx.moveTo(35, y)
        ctx.lineTo(width - 10, y)
        ctx.stroke()
      }

      // Draw Bar Chart
      const barWidth = (width - 60) / 12 - 6
      data.forEach((val, i) => {
        const barHeight = (val / maxVal) * (height - 50)
        const x = 40 + i * (barWidth + 6)
        const y = height - 30 - barHeight

        // Gradient Fill
        const gradient = ctx.createLinearGradient(0, y, 0, height - 30)
        gradient.addColorStop(0, '#7367f0')
        gradient.addColorStop(1, '#9e95f5')

        ctx.fillStyle = gradient
        ctx.beginPath()
        // Rounded bar top
        if (barHeight > 4) {
          ctx.roundRect ? ctx.roundRect(x, y, barWidth, barHeight, [4, 4, 0, 0]) : ctx.fillRect(x, y, barWidth, barHeight)
        } else {
          ctx.fillRect(x, y, barWidth, Math.max(barHeight, 2))
        }
        ctx.fill()

        // Month Labels
        ctx.fillStyle = '#82868b'
        ctx.font = '11px Inter, sans-serif'
        ctx.textAlign = 'center'
        ctx.fillText(months[i], x + barWidth / 2, height - 10)
      })
    },
    createOrdersChart() {
      const canvas = this.$refs.ordersChart
      if (!canvas) return
      const ctx = canvas.getContext('2d')

      const width = canvas.width = canvas.parentElement.clientWidth || 400
      const height = canvas.height = 240

      const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec']
      const data = months.map((_, i) => this.charts.monthly_orders[i + 1] || 0)
      const maxVal = Math.max(...data, 10)

      ctx.clearRect(0, 0, width, height)

      // Draw Line Chart
      ctx.strokeStyle = '#28c76f'
      ctx.lineWidth = 3
      ctx.beginPath()

      data.forEach((val, i) => {
        const x = 35 + (i / 11) * (width - 50)
        const y = height - 30 - (val / maxVal) * (height - 50)

        if (i === 0) ctx.moveTo(x, y)
        else ctx.lineTo(x, y)
      })
      ctx.stroke()

      // Points & Labels
      data.forEach((val, i) => {
        const x = 35 + (i / 11) * (width - 50)
        const y = height - 30 - (val / maxVal) * (height - 50)

        ctx.fillStyle = '#28c76f'
        ctx.beginPath()
        ctx.arc(x, y, 4, 0, 2 * Math.PI)
        ctx.fill()

        ctx.fillStyle = '#82868b'
        ctx.font = '11px Inter, sans-serif'
        ctx.textAlign = 'center'
        ctx.fillText(months[i], x, height - 10)
      })
    }
  }
}
</script>