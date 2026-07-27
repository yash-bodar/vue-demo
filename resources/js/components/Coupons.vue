<template>
  <div class="card card-vuexy p-4">
    <!-- Header -->
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-4 pb-3 border-bottom">
      <div class="d-flex align-items-center gap-3">
        <div class="badge bg-label-warning rounded-3 p-3">
          <i class="fas fa-ticket-alt fs-3"></i>
        </div>
        <div>
          <h4 class="mb-0 fw-bold text-heading">Coupons & Promos</h4>
          <small class="text-muted">Total {{ couponCount }} discount codes</small>
        </div>
      </div>

      <router-link to="/coupons/create" class="btn btn-primary rounded-pill px-4 shadow-primary">
        <i class="fas fa-plus me-1"></i>Create Coupon
      </router-link>
    </div>

    <!-- Filter Bar -->
    <div class="row g-3 mb-4">
      <div class="col-12 col-md-6">
        <div class="input-group">
          <span class="input-group-text bg-transparent border-end-0 text-muted"><i class="fas fa-search"></i></span>
          <input type="search" class="form-control border-start-0 ps-0" v-model="filters.search" placeholder="Search coupon code..." />
        </div>
      </div>
      <div class="col-6 col-md-3">
        <select class="form-select" v-model="filters.discount_type" @change="fetchCoupons(1)">
          <option value="">All Types</option>
          <option value="percentage">Percentage</option>
          <option value="fixed">Fixed Amount</option>
        </select>
      </div>
      <div class="col-6 col-md-3">
        <select class="form-select" v-model="filters.status" @change="fetchCoupons(1)">
          <option value="">All Status</option>
          <option value="active">Active</option>
          <option value="inactive">Inactive</option>
        </select>
      </div>
    </div>

    <!-- Table -->
    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0">
        <thead>
          <tr>
            <th>Coupon Code</th>
            <th>Type</th>
            <th>Discount</th>
            <th>Min Purchase</th>
            <th>Usage Limit</th>
            <th>Used</th>
            <th>Status</th>
            <th>Valid Until</th>
            <th class="text-end">Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr v-if="filteredCoupons.length > 0" v-for="coupon in filteredCoupons" :key="coupon.id">
            <td>
              <span class="badge bg-label-primary fs-7 fw-bold">{{ coupon.code }}</span>
            </td>
            <td>
              <span class="badge" :class="coupon.discount_type === 'percentage' ? 'bg-label-info' : 'bg-label-warning'">
                {{ coupon.discount_type === 'percentage' ? 'Percentage' : 'Fixed' }}
              </span>
            </td>
            <td class="fw-bold text-primary fs-7">
              {{ coupon.discount_type === 'percentage' ? `${coupon.discount_value}%` : `$${coupon.discount_value}` }}
            </td>
            <td class="fs-8 text-muted">
              {{ coupon.min_purchase_amount ? `$${coupon.min_purchase_amount}` : '-' }}
            </td>
            <td><span class="badge bg-label-secondary">{{ coupon.max_uses ? coupon.max_uses : 'Unlimited' }}</span></td>
            <td class="fw-bold fs-7">{{ coupon.times_used || 0 }}</td>
            <td>
              <span class="badge" :class="coupon.is_active ? 'bg-label-success' : 'bg-label-danger'">
                {{ coupon.is_active ? 'Active' : 'Inactive' }}
              </span>
            </td>
            <td class="fs-8 text-muted">{{ coupon.valid_until ? formatDate(coupon.valid_until) : 'Never' }}</td>
            <td class="text-end">
              <div class="d-inline-flex gap-1">
                <router-link :to="`/coupons/edit/${coupon.id}`" class="btn btn-sm btn-icon btn-label-primary" title="Edit">
                  <i class="fas fa-pen-to-square fs-8"></i>
                </router-link>
                <button type="button" class="btn btn-sm btn-icon btn-label-danger" @click="deleteCoupon(coupon.id)" title="Delete">
                  <i class="fas fa-trash-can fs-8"></i>
                </button>
              </div>
            </td>
          </tr>
          <tr v-else>
            <td colspan="9" class="text-center py-5 text-muted">
              <i class="fas fa-ticket-alt fa-3x mb-3 opacity-50"></i>
              <h6>No coupon codes found</h6>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script>
import { formatDate } from '../utils/formatDate'

export default {
  name: 'Coupons',
  data() {
    return {
      couponsList: [],
      couponCount: 0,
      loading: false,
      filters: {
        search: '',
        discount_type: '',
        status: '',
      }
    }
  },
  computed: {
    filteredCoupons() {
      return this.couponsList.filter(c => {
        const matchSearch = c.code.toLowerCase().includes(this.filters.search.toLowerCase())
        const matchType = !this.filters.discount_type || c.discount_type === this.filters.discount_type
        const matchStatus = !this.filters.status || (this.filters.status === 'active' ? c.is_active : !c.is_active)
        return matchSearch && matchType && matchStatus
      })
    }
  },
  mounted() {
    this.fetchCoupons()
  },
  methods: {
    formatDate,
    async fetchCoupons() {
      this.loading = true
      try {
        const response = await this.$axios.get('/api/coupons')
        if (response.data.success) {
          const resData = response.data.data
          this.couponsList = resData.data || resData || []
          this.couponCount = this.couponsList.length
        }
      } catch (err) {
        console.error('Fetch coupons error:', err)
      } finally {
        this.loading = false
      }
    },
    async deleteCoupon(id) {
      if (!confirm('Are you sure you want to delete this coupon?')) return
      try {
        const response = await this.$axios.delete(`/api/coupons/${id}`)
        if (response.data.success) {
          await this.fetchCoupons()
        }
      } catch (err) {
        console.error('Delete coupon error:', err)
      }
    }
  }
}
</script>