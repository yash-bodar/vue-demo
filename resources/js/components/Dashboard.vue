<template>
    <div class="dashboard p-3">
        <div class="dashboard-header mb-4">
            <h3 class="fw-bold mb-1">Dashboard</h3>
            <p class="text-muted">Welcome to your admin dashboard</p>
        </div>

        <div v-if="loading" class="text-center py-5">
            <div class="spinner-border" role="status"><span class="visually-hidden">Loading...</span></div>
        </div>
        <div v-else-if="error" class="alert alert-danger">{{ error }}</div>
        <div v-else>

            <!-- Statistics Cards -->
            <div class="row mb-4">
                <div class="col-md-3 col-sm-6 mb-3">
                    <div class="card shadow border-0 rounded-2 bg-primary text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div><h4 class="card-title fw-bold m-0">{{ stats.total_users }}</h4><p class="card-text m-0">Total Users</p></div>
                                <div class="align-self-center"><i class="fas fa-users fa-2x"></i></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-3 col-sm-6 mb-3">
                    <div class="card shadow border-0 rounded-2 bg-success text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div><h4 class="card-title fw-bold m-0">{{ stats.total_products }}</h4><p class="card-text m-0">Total Products</p></div>
                                <div class="align-self-center"><i class="fas fa-box fa-2x"></i></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-3 col-sm-6 mb-3">
                    <div class="card shadow border-0 rounded-2 bg-info text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div><h4 class="card-title fw-bold m-0">{{ stats.total_orders }}</h4><p class="card-text m-0">Total Orders</p></div>
                                <div class="align-self-center"><i class="fas fa-shopping-cart fa-2x"></i></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-3 col-sm-6 mb-3">
                    <div class="card shadow border-0 rounded-2 bg-warning text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div><h4 class="card-title fw-bold m-0">{{ formatCurrency(stats.total_revenue) }}</h4><p class="card-text m-0">Total Revenue</p></div>
                                <div class="align-self-center"><i class="fas fa-dollar-sign fa-2x"></i></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Additional Stats Row -->
            <div class="row mb-4">
                <div class="col-md-3 col-sm-6 mb-3">
                    <div class="card shadow border-0 rounded-2 bg-secondary text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div><h4 class="card-title fw-bold m-0">{{ stats.total_categories }}</h4><p class="card-text m-0">Categories</p></div>
                                <div class="align-self-center"><i class="fas fa-tags fa-2x"></i></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-3 col-sm-6 mb-3">
                    <div class="card shadow border-0 rounded-2 bg-danger text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div><h4 class="card-title fw-bold m-0">{{ stats.pending_orders }}</h4><p class="card-text m-0">Pending Orders</p></div>
                                <div class="align-self-center"><i class="fas fa-clock fa-2x"></i></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-3 col-sm-6 mb-3">
                    <div class="card shadow border-0 rounded-2 bg-dark text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div><h4 class="card-title fw-bold m-0">{{ stats.completed_orders }}</h4><p class="card-text m-0">Completed Orders</p></div>
                                <div class="align-self-center"><i class="fas fa-check-circle fa-2x"></i></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-3 col-sm-6 mb-3">
                    <div class="card shadow border-0 rounded-2 bg-light text-dark">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div><h4 class="card-title fw-bold m-0">{{ stats.active_products }}</h4><p class="card-text m-0">Active Products</p></div>
                                <div class="align-self-center"><i class="fas fa-check fa-2x"></i></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Charts Section -->
            <div class="row mb-4">
                <div class="col-md-6 mb-3">
                    <div class="card shadow border-0 rounded-2">
                        <div class="card-header">
                            <h5 class="card-title fw-bold m-0 mb-0">Monthly Revenue</h5>
                        </div>
                        <div class="card-body">
                            <canvas ref="revenueChart" width="400" height="200"></canvas>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 mb-3">
                    <div class="card shadow border-0 rounded-2">
                        <div class="card-header">
                            <h5 class="card-title fw-bold m-0 mb-0">Monthly Orders</h5>
                        </div>
                        <div class="card-body">
                            <canvas ref="ordersChart" width="400" height="200"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Orders and Top Products -->
            <div class="row">
                <div class="col-md-6 mb-3">
                    <div class="card shadow border-0 rounded-2">
                        <div class="card-header">
                            <h5 class="card-title fw-bold m-0 mb-0">Recent Orders</h5>
                        </div>
                        <div class="card-body">
                            <div v-if="recentOrders.length === 0" class="text-muted">
                                No recent orders
                            </div>
                            <div v-else>
                                <div v-for="order in recentOrders" :key="order.id" class="border-bottom py-2">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <strong>{{ order.user_name }}</strong>
                                            <div class="text-muted small">Order #{{ order.id }}</div>
                                        </div>
                                        <div class="text-end">
                                            <div>{{ formatCurrency(order.total_amount, order.currency) }}</div>
                                            <span :class="getStatusClass(order.status)" class="badge">{{ order.status }}</span>
                                        </div>
                                    </div>
                                    <div class="text-muted small">{{ order.created_at }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 mb-3">
                    <div class="card shadow border-0 rounded-2">
                        <div class="card-header">
                            <h5 class="card-title fw-bold m-0 mb-0">Latest Products</h5>
                        </div>
                        <div class="card-body">
                            <div v-if="topProducts.length === 0" class="text-muted">No products found</div>
                            <div v-else>
                                <div v-for="product in topProducts" :key="product.id" class="border-bottom py-2">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <strong>{{ product.name }}</strong><div class="text-muted small">{{ product.category }}</div>
                                        </div>
                                        <div class="text-end">
                                            <div>{{ formatCurrency(product.price, product.currency) }}</div>
                                            <span :class="product.status === 'active' ? 'badge bg-success' : 'badge bg-secondary'">{{ product.status }}</span>
                                        </div>
                                    </div>
                                    <div class="text-muted small">Stock: {{ product.stock }}</div>
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

                this.stats = data.stats
                this.recentOrders = data.recent_orders
                this.topProducts = data.top_products
                this.charts = data.charts
            } catch (error) {
                this.error = 'Failed to load dashboard data'
                console.error('Dashboard error:', error)
            } finally {
                this.loading = false
            }
        },

        formatCurrency(amount, currency = 'USD') {
            const signs = {
                'USD': '$',
                'EUR': '€',
                'CAD': 'C$',
                'INR': '₹',
                'AUD': 'A$',
                'AED': 'د.إ',
                'GBP': '£'
            }
            const sign = signs[currency] || '$'
            return `${sign}${parseFloat(amount).toFixed(2)}`
        },

        getStatusClass(status) {
            const classes = {
                'pending': 'bg-warning',
                'completed': 'bg-success',
                'cancelled': 'bg-danger',
                'processing': 'bg-info'
            }
            return classes[status] || 'bg-secondary'
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
            const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec']
            const data = months.map((month, index) => this.charts.monthly_revenue[index + 1] || 0)

            // Simple bar chart implementation
            const width = canvas.width
            const height = canvas.height
            const barWidth = width / 12 - 10
            const maxValue = Math.max(...data, 1)

            ctx.clearRect(0, 0, width, height)

            data.forEach((value, index) => {
                const barHeight = (value / maxValue) * (height - 40)
                const x = index * (barWidth + 10) + 5
                const y = height - barHeight - 20

                ctx.fillStyle = '#007bff'
                ctx.fillRect(x, y, barWidth, barHeight)

                ctx.fillStyle = '#666'
                ctx.font = '10px Arial'
                ctx.textAlign = 'center'
                ctx.fillText(months[index], x + barWidth / 2, height - 5)

                if (value > 0) {
                    ctx.fillText(this.formatCurrency(value), x + barWidth / 2, y - 5)
                }
            })
        },

        createOrdersChart() {
            const canvas = this.$refs.ordersChart
            if (!canvas) return

            const ctx = canvas.getContext('2d')
            const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec']
            const data = months.map((month, index) => this.charts.monthly_orders[index + 1] || 0)

            // Simple line chart implementation
            const width = canvas.width
            const height = canvas.height
            const maxValue = Math.max(...data, 1)

            ctx.clearRect(0, 0, width, height)

            ctx.strokeStyle = '#28a745'
            ctx.lineWidth = 2
            ctx.beginPath()

            data.forEach((value, index) => {
                const x = (index / (data.length - 1)) * (width - 40) + 20
                const y = height - ((value / maxValue) * (height - 40)) - 20

                if (index === 0) {
                    ctx.moveTo(x, y)
                } else {
                    ctx.lineTo(x, y)
                }

                // Draw point
                ctx.fillStyle = '#28a745'
                ctx.beginPath()
                ctx.arc(x, y, 4, 0, 2 * Math.PI)
                ctx.fill()

                // Draw month label
                ctx.fillStyle = '#666'
                ctx.font = '10px Arial'
                ctx.textAlign = 'center'
                ctx.fillText(months[index], x, height - 5)

                // Draw value
                if (value > 0) {
                    ctx.fillText(value, x, y - 10)
                }
            })

            ctx.stroke()
        }
    }
}
</script>