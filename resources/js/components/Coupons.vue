<template>
    <div class="card-header bg-gradient-primary text-light py-3 px-4 border-0 bg-primary-linear">
        <div class="row align-items-center filter-header">
            <div class="col-12 col-md-auto mt-1">
                <h5 class="mb-0 fw-bold d-flex align-items-center">
                    <i class="fas fa-ticket-alt me-2"></i>Coupons<span class="badge bg-white color-primary ms-2 px-2 py-1 small fw-semibold">{{ couponCount }}</span>
                </h5>
            </div>
            <div class="col-12 col-md-auto mt-1">
                <div class="search-input-wrapper">
                    <i class="fas fa-search search-icon"></i>
                    <input type="search" class="form-control p-1 ps-5" v-model="filters.search" placeholder="Search...">
                </div>
            </div>
            <div class="col-12 col-md-auto mt-1">
                <select class="form-select form-select-sm py-1 pe-5 w-auto" v-model="filters.discount_type" @change="fetchCoupons(1)">
                    <option value="">All Types</option>
                    <option value="percentage">Percentage</option>
                    <option value="fixed">Fixed Amount</option>
                </select>
            </div>
            <div class="col-12 col-md-auto mt-1">
                <select class="form-select form-select-sm py-1 pe-5 w-auto" v-model="filters.status" @change="fetchCoupons(1)">
                    <option value="">All Status</option>
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                </select>
            </div>
            <div class="col-12 col-md-auto ms-auto mt-1">
                <router-link class="btn btn-dark btn-sm p-2 shadow-sm" to="/coupons/create" title="Add New Coupon"><i class="fa fa-plus"></i></router-link>
            </div>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-container overflow-y-auto">
            <table class="table table-hover align-middle mb-0">
                <thead class="text-light bg-primary-linear sticky-top">
                    <tr>
                        <th @click="sortByField('code', 'fetchCoupons')" class="cursor-pointer ps-4">Code <i :class="getSortIcon('code')" class="ms-1"></i></th>
                        <th @click="sortByField('discount_type', 'fetchCoupons')" class="cursor-pointer">Type <i :class="getSortIcon('discount_type')" class="ms-1"></i></th>
                        <th @click="sortByField('discount_value', 'fetchCoupons')" class="cursor-pointer">Discount <i :class="getSortIcon('discount_value')" class="ms-1"></i></th>
                        <th @click="sortByField('min_purchase_amount', 'fetchCoupons')" class="cursor-pointer">Min Amount <i :class="getSortIcon('min_purchase_amount')" class="ms-1"></i></th>
                        <th>Max Uses</th>
                        <th>Uses</th>
                        <th @click="sortByField('is_active', 'fetchCoupons')" class="cursor-pointer">Status <i :class="getSortIcon('is_active')" class="ms-1"></i></th>
                        <th>Valid Until</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-if="couponsList.length > 0" v-for="coupon in couponsList" :key="coupon.id" class="hover-row">
                        <td class="ps-4">
                            <div class="d-flex align-items-center">
                                <span class="badge bg-primary">{{ coupon.code }}</span>
                            </div>
                        </td>
                        <td>
                            <span class="badge" :class="coupon.discount_type === 'percentage' ? 'bg-info' : 'bg-warning'">
                                {{ coupon.discount_type === 'percentage' ? 'Percentage' : 'Fixed' }}
                            </span>
                        </td>
                        <td>
                            <span class="fw-bold text-primary">
                                <span v-if="coupon.discount_type === 'percentage'">{{ coupon.discount_value }}%</span>
                                <span v-else>${{ coupon.discount_value }}</span>
                            </span>
                        </td>
                        <td>
                            <span v-if="coupon.min_purchase_amount" class="text-muted">${{ coupon.min_purchase_amount }}</span>
                            <span v-else class="text-muted">-</span>
                        </td>
                        <td>
                            <span class="badge bg-secondary">{{ coupon.max_uses ? coupon.max_uses : '∞' }}</span>
                        </td>
                        <td>
                            <span class="fw-semibold">{{ coupon.times_used }}</span>
                        </td>
                        <td>
                            <span class="badge" :class="coupon.is_active ? 'bg-success' : 'bg-secondary'">
                                {{ coupon.is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td>
                            <small>{{ coupon.valid_until ? formatDate(coupon.valid_until) : 'No limit' }}</small>
                        </td>
                        <td>
                            <div class="btn-group gap-2">
                                <router-link class="btn btn-sm btn-outline-primary p-2 fw-semibold rounded-1" :to="`/coupons/edit/${coupon.id}`" title="Edit">
                                    <i class="fa fa-pencil"></i>
                                </router-link>
                                <button class="btn btn-sm btn-outline-danger p-2 fw-semibold rounded-1" @click="deleteCoupon(coupon.id)" title="Delete">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    <tr v-else>
                        <td colspan="9" class="text-center py-5">
                            <i class="fas fa-inbox text-muted mb-3" style="font-size: 2rem;"></i>
                            <p class="text-muted">No coupons found</p>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>

<script>
import axios from 'axios'

export default {
    name: 'Coupons',
    data() {
        return {
            couponsList: [],
            couponCount: 0,
            loading: false,
            currentPage: 1,
            lastPage: 1,
            sortField: 'created_at',
            sortOrder: 'desc',
            filters: {
                search: '',
                discount_type: '',
                status: '',
            },
        }
    },
    computed: {
        filteredCoupons() {
            return this.couponsList.filter(coupon => {
                const matchSearch = coupon.code.toLowerCase().includes(this.filters.search.toLowerCase()) ||
                                  (coupon.description && coupon.description.toLowerCase().includes(this.filters.search.toLowerCase()))
                const matchType = !this.filters.discount_type || coupon.discount_type === this.filters.discount_type
                const matchStatus = !this.filters.status || (this.filters.status === 'active' ? coupon.is_active : !coupon.is_active)
                return matchSearch && matchType && matchStatus
            })
        }
    },
    mounted() {
        this.fetchCoupons(1)
    },
    methods: {
        async fetchCoupons(page = 1) {
            this.loading = true
            try {
                const response = await axios.get('/api/coupons')
                if (response.data.success) {
                    this.couponsList = response.data.data.data || response.data.data || []
                    this.couponCount = this.couponsList.length
                }
            } catch (error) {
                console.error('Failed to fetch coupons:', error)
            } finally {
                this.loading = false
            }
        },
        async deleteCoupon(id) {
            if (!confirm('Are you sure you want to delete this coupon?')) return

            try {
                const response = await axios.delete(`/api/coupons/${id}`)
                if (response.data.success) {
                    alert('Coupon deleted successfully')
                    this.fetchCoupons()
                }
            } catch (error) {
                alert(error.response?.data?.message || 'Failed to delete coupon')
            }
        },
        sortByField(field) {
            if (this.sortField === field) {
                this.sortOrder = this.sortOrder === 'asc' ? 'desc' : 'asc'
            } else {
                this.sortField = field
                this.sortOrder = 'asc'
            }
            this.fetchCoupons(1)
        },
        getSortIcon(field) {
            if (this.sortField !== field) return 'fas fa-sort text-muted'
            return this.sortOrder === 'asc' ? 'fas fa-sort-up text-primary' : 'fas fa-sort-down text-primary'
        },
        formatDate(date) {
            if (!date) return '-'
            return new Date(date).toLocaleDateString('en-US', {
                year: 'numeric',
                month: 'short',
                day: 'numeric',
            })
        },
    },
}
</script>