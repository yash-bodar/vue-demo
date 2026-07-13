<template>
    <div class="card-header bg-gradient-primary text-light py-3 px-4 border-0 bg-primary-linear">
        <div class="row align-items-center filter-header">
            <div class="col-12 col-md-auto mt-1">
                <h5 class="mb-0 fw-bold d-flex align-items-center">
                    <i class="fa fa-box-open me-2"></i>Orders<span class="badge bg-white color-primary ms-2 px-2 py-1 small fw-semibold">{{ orderCount }}</span>
                </h5>
            </div>
            <div class="col-12 col-md-auto mt-1">
                <div class="search-input-wrapper">
                    <i class="fas fa-search search-icon"></i>
                    <input type="search" class="form-control p-1 ps-5" v-model="filters.search" placeholder="Search...">
                </div>
            </div>
            <div class="col-12 col-md-auto mt-1">
                <select class="form-select form-select-sm py-1 pe-5 w-auto" v-model="filters.status" @change="fetchOrders(1)">
                    <option value="">All Status</option>
                    <option value="completed">Completed</option>
                    <option value="delivered">Delivered</option>
                    <option value="processing">Processing</option>
                    <option value="pending">Pending</option>
                    <option value="cancelled">Cancelled</option>
                    <option value="shipped">Shipped</option>
                    <option value="refunded">Refunded</option>
                </select>
            </div>
            <div class="col-12 col-md-auto mt-1">
                <select class="form-select form-select-sm py-1 pe-5 w-auto" v-model="filters.payment_status" @change="fetchOrders(1)">
                    <option value="">All Payment Status</option>
                    <option value="paid">Paid</option>
                    <option value="pending">Pending</option>
                    <option value="cancelled">Cancelled</option>
                </select>
            </div>
            <div class="col-12 col-md-auto mt-1">
                <select class="form-select form-select-sm py-1 pe-5 w-auto" v-model="filters.currency" @change="fetchOrders(1)">
                    <option value="">All Currency</option>
                    <option value="USD">USD - US Dollar ($)</option>
                    <option value="EUR">EUR - Euro (€)</option>
                    <option value="GBP">GBP - British Pound (£)</option>
                    <option value="CAD">CAD - Canadian Dollar (C$)</option>
                    <option value="AUD">AUD - Australian Dollar (A$)</option>
                    <option value="INR">INR - Indian Rupee (₹)</option>
                    <option value="AED">AED - UAE Dirham (د.إ)</option>
                </select>
            </div>
            <div class="col-12 col-md-auto mt-1">
                <button class="btn btn-outline-light btn-sm p-2 shadow-sm me-2" @click="exportData('pdf')" title="Export PDF" ><i class="fa fa-file-pdf"></i></button>
                <button class="btn btn-outline-light btn-sm p-2 shadow-sm me-2" @click="exportData('csv')" title="Export CSV" ><i class="fa fa-file-csv"></i></button>
            </div>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-container overflow-y-auto">
            <table class="table table-hover align-middle mb-0">
                <thead class="text-light bg-primary-linear sticky-top">
                    <tr>
                        <th @click="$sortByField('user_id', 'fetchOrders')" class="cursor-pointer">User <i :class="$getSortIcon('user_id')" class="ms-1"></i></th>
                        <!-- <th @click="$sortByField('product_count', 'fetchOrders')" class="cursor-pointer">No. of Products <i :class="$getSortIcon('product_count')" class="ms-1"></i></th> -->
                        <th>No. of Products</th>
                        <th @click="$sortByField('currency', 'fetchOrders')" class="cursor-pointer">Currency <i :class="$getSortIcon('currency')" class="ms-1"></i></th>
                        <th @click="$sortByField('total_amount', 'fetchOrders')" class="cursor-pointer">Total <i :class="$getSortIcon('total_amount')" class="ms-1"></i></th>
                        <th @click="$sortByField('status', 'fetchOrders')" class="cursor-pointer">Status <i :class="$getSortIcon('status')" class="ms-1"></i></th>
                        <th @click="$sortByField('payment_status', 'fetchOrders')" class="cursor-pointer">Payment Status <i :class="$getSortIcon('payment_status')" class="ms-1"></i></th>
                        <th @click="$sortByField('created_at','fetchOrders')" class="cursor-pointer">Created At <i :class="$getSortIcon('created_at')" class="ms-1"></i></th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-if="ordersList.length > 0" v-for="order in ordersList" :key="order.id"
                        class="hover-row">
                        <td>
                            <div class="d-flex align-items-center gap-3">
                                <div>
                                    <div class="fw-semibold text-primary">{{ order.user?.name || 'N/A' }}</div>
                                    <small class="text-muted">ID: #{{ order.id }}</small>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="fw-bold text-primary">{{ order.items?.length }}</span>
                        </td>
                        <td>
                            <span class="badge bg-secondary">{{ order.currency }}</span>
                        </td>
                        <td>
                            <span class="fw-bold text-primary">{{ order.currency_sign }} {{ order.total_amount }}</span>
                        </td>
                        <td>
                            <div class="position-relative">
                                <span class="badge cursor-pointer" @click="toggleStatusDropdown(order.id)" :class="$getOrderStatusBadgeClass(order.status)">{{ order.status }}</span>
                                <div v-if="openStatusDropdown === order.id" class="status-dropdown position-absolute bg-white shadow rounded p-2 mt-1" style="z-index: 1000; min-width: 150px;">
                                    <div v-for="status in statusOptions" :key="status.value" class="dropdown-item py-1 px-2 cursor-pointer rounded"
                                         :class="{'bg-light': status.value === order.status}" @click="updateOrderStatus(order.id, status.value)">
                                        <span class="badge me-2" :class="$getOrderStatusBadgeClass(status.value)">{{ status.value }}</span>
                                        {{ status.label }}
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="position-relative">
                                <span class="badge cursor-pointer" @click="togglePaymentStatusDropdown(order.id)" :class="$getOrderStatusBadgeClass(order.payment_status)">{{ order.payment_status }}</span>
                                <div v-if="openPaymentStatusDropdown === order.id" class="status-dropdown position-absolute bg-white shadow rounded p-2 mt-1" style="z-index: 1000; min-width: 150px;">
                                    <div v-for="status in paymentStatusOptions" :key="status.value" class="dropdown-item py-1 px-2 cursor-pointer rounded"
                                         :class="{'bg-light': status.value === order.payment_status}" @click="updatePaymentStatus(order.id, status.value)">
                                        <span class="badge me-2" :class="$getOrderStatusBadgeClass(status.value)">{{ status.value }}</span>
                                        {{ status.label }}
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <i class="fa fa-calendar text-muted"></i>
                                <span>{{ $formatDate(order.created_at) }}</span>
                            </div>
                        </td>
                        <td>
                            <div class="btn-group gap-2">
                                <button class="btn btn-sm btn-outline-primary p-2 fw-semibold rounded-1" @click="viewOrder(order.id)" type="button" title="View Details">
                                    <i class="fa fa-eye"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-primary p-2 fw-semibold rounded-1" @click="downloadInvoice(order.id)" type="button" title="Download Invoice">
                                    <i class="fa fa-file-invoice"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    <tr v-else>
                        <td colspan="9" class="text-center py-5">
                            <div class="text-muted">
                                <i class="fa fa-box fs-1 d-block mb-3"></i>
                                <h5>No orders found</h5>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="card-footer bg-white border-0 p-3" v-if="lastPage > 1">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 pagination-div">
                <span class="text-muted small">Showing {{ (currentPage - 1) * perPage + 1 }} to {{ Math.min(currentPage * perPage, orderCount) }} of {{ orderCount }}</span>
                <div class="d-flex gap-1">
                    <button class="btn btn-sm btn-outline-secondary rounded-2" :disabled="currentPage === 1" @click="fetchOrders(currentPage - 1)"><i class="fa fa-chevron-left fa-xs"></i></button>
                    <button v-for="page in lastPage" :key="page" :disabled="page === currentPage" class="btn opacity-100 rounded-2 btn-sm"
                        :class="page === currentPage ? 'bg-primary btn-outline-primary text-white' : 'btn-outline-secondary'" @click="fetchOrders(page)">
                        {{ page }}
                    </button>
                    <button class="btn btn-sm btn-outline-secondary rounded-2" :disabled="currentPage === lastPage"
                        @click="fetchOrders(currentPage + 1)">
                        <i class="fa fa-chevron-right fa-xs"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    data() {
        return {
            ordersList: [],
            orderCount: 0,
            currentPage: 1,
            perPage: 10,
            lastPage: 1,
            openStatusDropdown: null,
            openPaymentStatusDropdown: null,
            statusOptions: [
                { value: 'pending', label: 'Pending' },
                { value: 'processing', label: 'Processing' },
                { value: 'shipped', label: 'Shipped' },
                { value: 'delivered', label: 'Delivered' },
                { value: 'completed', label: 'Completed' },
                { value: 'cancelled', label: 'Cancelled' },
                { value: 'refunded', label: 'Refunded' }
            ],
            paymentStatusOptions: [
                { value: 'paid', label: 'Paid' },
                { value: 'pending', label: 'Pending' },
                { value: 'cancelled', label: 'Cancelled' }
            ],
            filters: {
                status: '',
                payment_status: '',
                currency: '',
                sort_by: 'id',
                sort_dir: 'desc',
                search: ''
            }
        }
    },
    watch: {
        'filters.search'(newSearch, oldSearch) {
            this.fetchOrders(1); 
        }
    },
    mounted() {
        this.fetchOrders();
        // Add click outside listener to close dropdown
        document.addEventListener('click', this.handleClickOutside);
    },
    beforeUnmount() {
        // Remove click outside listener
        document.removeEventListener('click', this.handleClickOutside);
    },
    methods: {
        exportData(type = 'pdf'){
            const params = new URLSearchParams({
                type: type,
                ...this.filters
            });
            this.$axios.get(`/api/export-orders?${params.toString()}`, {
                responseType: 'blob'
            }).then(res => {
                const url = window.URL.createObjectURL(new Blob([res.data]));
                const link = document.createElement('a');
                link.href = url;
                link.setAttribute('download', `orders.${type}`);
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
                window.URL.revokeObjectURL(url);
            }).catch(err => console.error('Failed to export orders', err));
        },
        fetchOrders(page = 1) {
            const params = new URLSearchParams({
                page: page,
                ...this.filters
            });
            this.$axios.get(`/api/orders?${params.toString()}`)
                .then(res => {
                    this.ordersList = res.data.data.data
                    this.orderCount = res.data.data.total
                    this.currentPage = res.data.data.current_page
                    this.lastPage = res.data.data.last_page
                    this.perPage = res.data.data.per_page
                })
                .catch(err => console.error('Failed to fetch orders', err))

        },
        viewOrder(id) {
            this.$router.push(`/orders/detail/${id}`);
        },
        async downloadInvoice(id) {
            this.$axios.get(`/api/download-invoice/${id}`, {
                responseType: 'blob'
            }).then(res => {
                const url = window.URL.createObjectURL(new Blob([res.data]));
                const link = document.createElement('a');
                link.href = url;
                link.setAttribute('download', `${id}-invoice.pdf`);
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
                window.URL.revokeObjectURL(url);
            }).catch(err => console.error('Failed to download invoice', err));
        },
        toggleStatusDropdown(orderId) {
            this.openStatusDropdown = this.openStatusDropdown === orderId ? null : orderId;
        },
        togglePaymentStatusDropdown(orderId) {
            this.openPaymentStatusDropdown = this.openPaymentStatusDropdown === orderId ? null : orderId;
        },
        handleClickOutside(event) {
            // Close dropdown if clicking outside of any status dropdown
            if (!event.target.closest('.status-dropdown') && !event.target.closest('.badge')) {
                this.openStatusDropdown = null;
                this.openPaymentStatusDropdown = null;
            }
        },
        async updateOrderStatus(orderId, newStatus) {
            try {
                const response = await this.$axios.put(`/api/orders/${orderId}/status`, {
                    status: newStatus
                });
                if (response.data.success) {
                    const orderIndex = this.ordersList.findIndex(o => o.id === orderId);
                    if (orderIndex !== -1) {
                        this.ordersList[orderIndex].status = newStatus;
                    }
                    this.openStatusDropdown = null;
                    this.$toast(response.data.message ?? 'Order status updated successfully!', 'success');
                }
            } catch (error) {
                console.error('Failed to update order status:', error);
                this.$toast('Failed to update order status', 'error');
            }
        },
        async updatePaymentStatus(orderId, newStatus) {
            try {
                const response = await this.$axios.put(`/api/orders/${orderId}/payment-status`, {
                    payment_status: newStatus
                });
                if (response.data.success) {
                    const orderIndex = this.ordersList.findIndex(o => o.id === orderId);
                    if (orderIndex !== -1) {
                        this.ordersList[orderIndex].payment_status = newStatus;
                    }
                    this.openPaymentStatusDropdown = null;
                    this.$toast(response.data.message ?? 'Payment status updated successfully!', 'success');
                }
            } catch (error) {
                console.error('Failed to update payment status:', error);
                this.$toast('Failed to update payment status', 'error');
            }
        }
    }
}
</script>