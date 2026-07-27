<template>
  <div class="container-xl py-4">
    <!-- Header -->
    <div class="card card-vuexy p-4 mb-4">
      <div class="d-flex align-items-center gap-3">
        <div class="badge bg-label-primary rounded-3 p-3">
          <i class="fas fa-user-gear fs-3"></i>
        </div>
        <div>
          <h4 class="mb-0 fw-bold text-heading">Account & Settings</h4>
          <small class="text-muted">Manage personal information, password & preferences</small>
        </div>
      </div>
    </div>

    <!-- Main Grid -->
    <div class="row g-4">
      <!-- Left Column: User Summary Card -->
      <div class="col-lg-4">
        <div class="card card-vuexy p-4 text-center mb-4">
          <img :src="profileImage" class="rounded-circle border border-3 border-primary shadow-sm mx-auto mb-3" width="100" height="100" alt="Avatar" />
          <h5 class="fw-bold text-heading mb-1">{{ user?.name || 'User Name' }}</h5>
          <p class="text-muted fs-7 mb-3">{{ user?.email || 'user@example.com' }}</p>
          <div class="d-flex justify-content-center gap-2 mb-4">
            <span class="badge bg-label-primary text-uppercase fs-9">{{ user?.role || 'User' }}</span>
            <span class="badge" :class="user?.status === 'Active' ? 'bg-label-success' : 'bg-label-danger'">
              {{ user?.status || 'Active' }}
            </span>
          </div>

          <div class="border-top pt-3 text-start fs-7">
            <div class="d-flex justify-content-between mb-2">
              <span class="text-muted"><i class="far fa-calendar me-2"></i>Member Since</span>
              <span class="fw-semibold text-heading">{{ formatDate(user?.created_at) }}</span>
            </div>
            <div class="d-flex justify-content-between">
              <span class="text-muted"><i class="fas fa-dollar-sign me-2"></i>Currency</span>
              <span class="fw-semibold text-heading">{{ user?.currency || 'USD' }} ({{ user?.currency_sign || '$' }})</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Right Column: Profile Edit & Change Password Forms -->
      <div class="col-lg-8">
        <!-- Edit Profile Card -->
        <div class="card card-vuexy p-4 mb-4">
          <h5 class="fw-bold text-heading mb-4"><i class="fas fa-pen-to-square me-2 text-primary"></i>Personal Details</h5>

          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label fs-7 fw-semibold">Full Name</label>
              <input type="text" class="form-control" v-model="form.name" :class="{ 'is-invalid': errors.name }" />
              <div class="invalid-feedback" v-if="errors.name">{{ errors.name }}</div>
            </div>

            <div class="col-md-6">
              <label class="form-label fs-7 fw-semibold">Email Address</label>
              <input type="email" class="form-control" v-model="form.email" :class="{ 'is-invalid': errors.email }" />
              <div class="invalid-feedback" v-if="errors.email">{{ errors.email }}</div>
            </div>

            <div class="col-md-12">
              <label class="form-label fs-7 fw-semibold">Preferred Currency</label>
              <select class="form-select" v-model="form.currency">
                <option value="USD">USD - US Dollar ($)</option>
                <option value="EUR">EUR - Euro (€)</option>
                <option value="GBP">GBP - British Pound (£)</option>
                <option value="CAD">CAD - Canadian Dollar (C$)</option>
                <option value="AUD">AUD - Australian Dollar (A$)</option>
                <option value="INR">INR - Indian Rupee (₹)</option>
                <option value="AED">AED - UAE Dirham (د.إ)</option>
              </select>
            </div>

            <div class="col-12 mt-4 d-flex gap-2">
              <button type="button" @click="updateProfile" class="btn btn-primary rounded-pill px-4 fw-bold shadow-primary" :disabled="loading">
                <i class="fas fa-check me-2"></i>Save Changes
              </button>
              <button type="button" class="btn btn-outline-secondary rounded-pill px-4" @click="resetForm">
                Reset
              </button>
            </div>
          </div>
        </div>

        <!-- Change Password Card -->
        <div class="card card-vuexy p-4">
          <h5 class="fw-bold text-heading mb-4"><i class="fas fa-lock me-2 text-warning"></i>Security & Password</h5>

          <div class="row g-3">
            <div class="col-md-12">
              <label class="form-label fs-7 fw-semibold">Current Password</label>
              <input type="password" class="form-control" v-model="passwordForm.current_password" :class="{ 'is-invalid': passwordErrors.current_password }" />
              <div class="invalid-feedback" v-if="passwordErrors.current_password">{{ passwordErrors.current_password }}</div>
            </div>

            <div class="col-md-6">
              <label class="form-label fs-7 fw-semibold">New Password</label>
              <input type="password" class="form-control" v-model="passwordForm.new_password" :class="{ 'is-invalid': passwordErrors.new_password }" />
              <div class="invalid-feedback" v-if="passwordErrors.new_password">{{ passwordErrors.new_password }}</div>
            </div>

            <div class="col-md-6">
              <label class="form-label fs-7 fw-semibold">Confirm New Password</label>
              <input type="password" class="form-control" v-model="passwordForm.password_confirmation" :class="{ 'is-invalid': passwordErrors.password_confirmation }" />
              <div class="invalid-feedback" v-if="passwordErrors.password_confirmation">{{ passwordErrors.password_confirmation }}</div>
            </div>

            <div class="col-12 mt-4">
              <button type="button" class="btn btn-warning text-white rounded-pill px-4 fw-bold" :disabled="passwordLoading" @click="changePassword">
                <i class="fas fa-key me-2"></i>Update Password
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { mapState, mapActions } from 'pinia'
import { useAuthStore } from '../stores/auth'

export default {
  name: 'Profile',
  data() {
    return {
      loading: false,
      passwordLoading: false,
      form: {
        name: '',
        email: '',
        currency: 'USD'
      },
      errors: {},
      passwordForm: {
        current_password: '',
        new_password: '',
        password_confirmation: ''
      },
      passwordErrors: {}
    }
  },
  computed: {
    ...mapState(useAuthStore, ['user']),
    profileImage() {
      return `https://ui-avatars.com/api/?name=${encodeURIComponent(this.user?.name || 'User')}&background=7367f0&color=fff&size=150`
    }
  },
  mounted() {
    this.initForm()
  },
  methods: {
    ...mapActions(useAuthStore, ['fetchUser']),
    initForm() {
      if (this.user) {
        this.form.name = this.user.name || ''
        this.form.email = this.user.email || ''
        this.form.currency = this.user.currency || 'USD'
      }
    },
    resetForm() {
      this.initForm()
      this.errors = {}
    },
    formatDate(d) {
      if (!d) return 'N/A'
      return new Date(d).toLocaleDateString(undefined, { month: 'short', day: 'numeric', year: 'numeric' })
    },
    async updateProfile() {
      this.loading = true
      this.errors = {}
      try {
        const response = await this.$axios.post('/api/update-profile', this.form)
        if (response.data.success) {
          await this.fetchUser()
          alert('Profile updated successfully!')
        }
      } catch (error) {
        if (error.response && error.response.status === 422) {
          this.errors = error.response.data.errors || {}
        } else {
          alert('Failed to update profile.')
        }
      } finally {
        this.loading = false
      }
    },
    async changePassword() {
      this.passwordLoading = true
      this.passwordErrors = {}
      try {
        const response = await this.$axios.post('/api/change-password', this.passwordForm)
        if (response.data.success) {
          alert('Password changed successfully!')
          this.passwordForm = { current_password: '', new_password: '', password_confirmation: '' }
        }
      } catch (error) {
        if (error.response && error.response.status === 422) {
          this.passwordErrors = error.response.data.errors || {}
        } else {
          alert('Failed to change password.')
        }
      } finally {
        this.passwordLoading = false
      }
    }
  }
}
</script>
