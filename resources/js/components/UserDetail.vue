<template>
    <div class="user-container">
        <div class="user-header bg-white m-2 border-bottom shadow-sm p-3 mx-4 my-3 rounded-2">
            <div class="row align-items-center g-3">
                <div class="col-12 col-md-6">
                    <div class="d-flex align-items-center">
                        <div class="me-3">
                            <i class="fas fa-user fa-2x text-primary"></i>
                        </div>
                        <div>
                            <h5 class="mb-0 fw-bold text-dark">User Details</h5>
                            <p class="mb-0 text-muted small">View user information and orders</p>
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

        <!-- Body -->
        <div class="user-body p-4">
            <!-- Loading State -->
            <div v-if="loading" class="text-center py-5">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="mt-3 text-muted">Loading user details...</p>
            </div>

            <!-- User Content -->
            <div v-else-if="user">
                <div class="row">
                    <!-- Card 1: Profile Card -->
                    <div class="col-md-4 col-sm-12 col-lg-4">
                        <div class="card shadow-sm border-0 rounded-3 mb-4">
                            <div class="card-body text-center">
                                <div class="position-relative d-inline-block mb-3">
                                    <img :src="profileImage" class="rounded-circle border-4 border-white shadow-lg user-avatar" alt="Profile Picture">
                                </div>
                                <h4 class="fw-bold mb-2">{{ user.name }}</h4>
                                <p class="text-muted mb-3">{{ user.email || 'N/A' }}</p>
                                <div class="mb-4">
                                    <span class="badge bg-primary me-2">{{ user.role || 'user' }}</span>
                                    <span class="badge" :class="user.status === 'Active' ? 'bg-success' : 'bg-secondary'">
                                        {{ user.status }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Card 2: User Information Card -->
                    <div class="col-md-8 col-sm-12 col-lg-8">
                        <div class="card shadow-sm border-0 rounded-3 mb-4">
                            <div class="card-header bg-white py-3 border-bottom">
                                <h5 class="mb-0 fw-semibold"><i class="fas fa-info-circle me-2"></i>User Information</h5>
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-md-6 col-sm-12">
                                        <div class="info-box p-2 bg-light rounded-3">
                                            <small class="text-muted d-block mb-1">Full Name</small>
                                            <span class="fw-bold">{{ user.name || 'N/A' }}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-sm-12">
                                        <div class="info-box p-2 bg-light rounded-3">
                                            <small class="text-muted d-block mb-1">Email Address</small>
                                            <span class="fw-bold">{{ user.email || 'N/A' }}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-sm-12">
                                        <div class="info-box p-2 bg-light rounded-3">
                                            <small class="text-muted d-block mb-1">Member Since</small>
                                            <span class="fw-bold">{{ formatDate(user.created_at) }}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-sm-12">
                                        <div class="info-box p-2 bg-light rounded-3">
                                            <small class="text-muted d-block mb-1">Total Orders</small>
                                            <span class="fw-bold">{{ ordersCount }}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-sm-12">
                                        <div class="info-box p-2 bg-light rounded-3">
                                            <small class="text-muted d-block mb-1">Preferred Currency</small>
                                            <span class="fw-bold">{{ user.currency || 'USD' }} {{ user.currency_sign || '$' }}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-sm-12">
                                        <div class="text-center mt-2">
                                            <router-link class="btn btn-outline-primary rounded-2 px-4 w-100" :to="`/users/edit/${user.id}`">
                                                <i class="fas fa-edit me-2"></i>Edit User
                                            </router-link>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Orders Section -->
                <div class="card shadow-sm rounded-3 border-0 overflow-hidden">
                    <div class="card-header bg-gradient-primary text-light py-3 px-4 border-0 bg-primary-linear">
                        <div class="row align-items-center">
                            <div class="col-12 col-md-auto">
                                <h5 class="mb-0 fw-bold d-flex align-items-center">
                                    <i class="fas fa-shopping-bag me-2"></i>User Orders
                                    <span class="badge bg-white color-primary ms-2 px-2 py-1 small fw-semibold">{{ ordersCount }}</span>
                                </h5>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-container overflow-y-auto">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="text-light bg-primary-linear sticky-top">
                                    <tr>
                                        <th>Order ID</th>
                                        <th>No. of Products</th>
                                        <th>Currency</th>
                                        <th>Total</th>
                                        <th>Status</th>
                                        <th>Payment Status</th>
                                        <th>Created At</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-if="ordersList.length > 0" v-for="order in ordersList" :key="order.id" class="hover-row">
                                        <td>
                                            <div class="fw-semibold text-primary">#{{ order.id }}</div>
                                        </td>
                                        <td>
                                            <span class="fw-bold text-primary">{{ order.items?.length || 0 }}</span>
                                        </td>
                                        <td>
                                            <span class="badge bg-secondary">{{ order.currency }}</span>
                                        </td>
                                        <td>
                                            <span class="fw-bold text-primary">{{ order.currency_sign || '$' }} {{ order.total_amount }}</span>
                                        </td>
                                        <td>
                                            <span class="badge" :class="getOrderStatusBadgeClass(order.status)">{{ order.status }}</span>
                                        </td>
                                        <td>
                                            <span class="badge" :class="getOrderStatusBadgeClass(order.payment_status)">{{ order.payment_status }}</span>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                <i class="fa fa-calendar text-muted"></i>
                                                <span>{{ formatDate(order.created_at) }}</span>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="btn-group gap-2">
                                                <button class="btn btn-sm btn-outline-primary p-2 fw-semibold rounded-1" @click="$router.push(`/orders/detail/${order.id}`)" type="button" title="View Details">
                                                    <i class="fa fa-eye"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr v-else>
                                        <td colspan="8" class="text-center py-5">
                                            <div class="text-muted">
                                                <i class="fa fa-shopping-bag fs-1 d-block mb-3"></i>
                                                <h5>No orders found</h5>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="card-footer bg-white border-0 p-3" v-if="lastPage > 1">
                            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 pagination-div">
                                <span class="text-muted small">Showing {{ (currentPage - 1) * perPage + 1 }} to {{ Math.min(currentPage * perPage, ordersCount) }} of {{ ordersCount }}</span>
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
                </div>
            </div>

            <!-- Empty State -->
            <div v-else class="text-center py-5">
                <i class="fas fa-user-slash fa-4x text-muted mb-3"></i>
                <h5 class="text-muted">User not found</h5>
                <p class="text-muted mb-4">The user you're looking for doesn't exist.</p>
                <button class="btn btn-primary bg-primary-linear" @click="$router.push('/users')">
                    <i class="fas fa-arrow-left me-2"></i>Back to Users
                </button>
            </div>
        </div>
    </div>
</template>

<script>
import { formatDate } from '../utils/formatDate'
import { getOrderStatusBadgeClass } from '../utils/statusBadge'
import { showToast } from '../utils/ui-toasts'

export default {
    data() {
        return {
            loading: false,
            user: null,
            profileImage: null,
            ordersList: [],
            ordersCount: 0,
            currentPage: 1,
            perPage: 10,
            lastPage: 1
        }
    },
    mounted() {
        this.fetchUser()
    },
    methods: {
        fetchUser() {
            this.loading = true;
            const id = this.$route.params.id;
            this.$axios.get(`/api/fetch-user/${id}`)
                .then(res => {
                    this.user = res.data.data;
                    this.profileImage= `https://ui-avatars.com/api/?name=${this.user?.name || 'User'}&background=0d6efd&color=fff&size=150`,

                    this.fetchOrders();
                })
                .catch(err => console.error('Failed to fetch user', err))
                .finally(() => {
                    this.loading = false;
                });
        },
        async fetchOrders(page = 1) {
            const id = this.$route.params.id;
            const params = new URLSearchParams({
                page: page,
                user_id: id
            });
            const response = await this.$axios.get(`/api/get-orders?${params.toString()}`);
            const data = response.data;
            console.log(data);
            if (data.success) {
                this.ordersList = data.data.data;
                this.ordersCount = data.data.total;
                this.currentPage = data.data.current_page;
                this.perPage = data.data.per_page;
                this.lastPage = data.data.last_page;
            }
        },
        deleteUser(id) {
            if (!confirm('Are you sure you want to delete this user?')) return
            this.$axios.delete('/api/users/' + id)
                .then(res => {
                    if (res.data.success === true) {
                        showToast('User deleted successfully!', 'success');
                        this.$router.push('/users');
                    } else {
                        showToast('Delete failed', 'error');
                    }
                })
                .catch(err => {
                    console.error('Error deleting user', err);
                    showToast('Error deleting user', 'error');
                });
        },
        formatDate,
        getOrderStatusBadgeClass
    }
}
</script>
