<template>
  <div class="products-container">
    <!-- Header -->
    <div class="profile-header bg-white m-2 border-bottom shadow-sm p-3 mx-4 my-3 rounded-2">
      <div class="row align-items-center g-3">
        <div class="col-12 col-md-6">
          <div class="d-flex align-items-center">
            <div class="me-3">
              <i class="fas fa-user fa-2x text-primary p-1"></i>
            </div>
            <div>
              <h5 class="mb-0 fw-bold text-dark">My Profile</h5>
              <p class="mb-0 text-muted small">Manage your account</p>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Body -->
    <div class="profile-body p-4">
      <div class="row">
        <div class="col-md-6 col-sm-12 col-lg-4">
          <div class="card shadow-sm border-0 rounded-3 mb-2">
            <div class="card-body text-center">
              <div class="position-relative d-inline-block">
                <img :src="profileImage" class="rounded-circle border-4 border-white shadow-lg user-avatar" alt="Profile Picture">
              </div>
              <h4 class="mt-3 mb-1">{{ user?.name || 'User Name' }}</h4>
              <p class="text-muted mb-0">{{ user?.email || 'user@example.com' }}</p>
              <div class="mt-2">
                <span class="badge bg-primary me-2">{{ user?.role || 'user' }}</span>
                <span class="badge" :class="user?.status === 'Active' ? 'bg-success' : 'bg-danger'">
                  {{ user?.status || 'Active' }}
                </span>
              </div>
            </div>
          </div>
          <div class="card shadow-sm border-0 rounded-3 mb-2">
            <div class="card-body">
              <div class="d-flex align-items-center mb-4">
                <div class="flex-shrink-0">
                  <div class="bg-warning bg-opacity-10 rounded-circle p-3">
                    <i class="bi bi-calendar3 text-primary fs-4"></i>
                  </div>
                </div>
                <div class="flex-grow-1 ms-3">
                  <h6 class="mb-1 text-muted">Member Since</h6>
                  <p class="mb-0 fw-semibold">{{ formatDate(user?.created_at) }}</p>
                </div>
              </div>
              <div class="d-flex align-items-center">
                <div class="flex-shrink-0">
                  <div class="bg-success bg-opacity-10 rounded-circle p-3">
                    <i class="bi bi-currency-exchange text-success fs-4"></i>
                  </div>
                </div>
                <div class="flex-grow-1 ms-3">
                  <h6 class="mb-1 text-muted">Preferred Currency</h6>
                  <p class="mb-0 fw-semibold">{{ user?.currency || 'USD' }} {{ user?.currency_sign || '$' }}</p>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-md-6 col-sm-12 col-lg-4">
          <!-- Edit Profile Section -->
          <div class="card shadow-sm border-0 rounded-3 mb-2">
            <div class="card-header bg-white py-3 border-bottom">
              <h5 class="mb-0 fw-semibold"><i class="bi bi-person-gear me-2"></i>Edit Profile</h5>
            </div>
            <div class="card-body">
              <div class="row g-3">
                <div class="col-md-12">
                  <label for="name" class="form-label fw-semibold">Full Name</label>
                  <input type="text" class="form-control" id="name" v-model="form.name"
                    :class="{ 'is-invalid': errors.name }">
                  <div class="invalid-feedback" v-if="errors.name">{{ errors.name }}</div>
                </div>
                <div class="col-md-12">
                  <label for="email" class="form-label fw-semibold">Email Address</label>
                  <input type="email" class="form-control" id="email" v-model="form.email"
                    :class="{ 'is-invalid': errors.email }">
                  <div class="invalid-feedback" v-if="errors.email">{{ errors.email }}</div>
                </div>
                <div class="col-md-12">
                  <label for="currency" class="form-label fw-semibold">Preferred Currency</label>
                  <select class="form-select" id="currency" v-model="form.currency"
                    :class="{ 'is-invalid': errors.currency }">
                    <option value="USD">USD - US Dollar ($)</option>
                    <option value="EUR">EUR - Euro (€)</option>
                    <option value="GBP">GBP - British Pound (£)</option>
                    <option value="CAD">CAD - Canadian Dollar (C$)</option>
                    <option value="AUD">AUD - Australian Dollar (A$)</option>
                    <option value="INR">INR - Indian Rupee (₹)</option>
                    <option value="AED">AED - UAE Dirham (د.إ)</option>
                  </select>
                  <div class="invalid-feedback" v-if="errors.currency">{{ errors.currency }}</div>
                </div>
                <div class="col-12">
                  <div class="d-flex flex-column gap-2">
                    <button type="button" @click="updateProfile" class="btn btn-primary bg-primary-linear w-100"
                      :disabled="loading">
                      <span v-if="loading" class="spinner-border spinner-border-sm me-2"></span><i
                        class="bi bi-check-circle me-2"></i>Update Profile
                    </button>
                    <button type="button" class="btn btn-outline-secondary w-100" @click="resetForm"><i
                        class="bi bi-arrow-clockwise me-2"></i>Reset</button>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-md-6 col-sm-12 col-lg-4">
          <div class="card shadow-sm border-0 rounded-3 mb-2">
            <div class="card-header bg-white py-3 border-bottom">
              <h5 class="mb-0 fw-semibold"><i class="bi bi-shield-lock me-2"></i>Change Password</h5>
            </div>
            <div class="card-body">
              <div class="row g-3">
                <div class="col-md-12">
                  <label for="current_password" class="form-label fw-semibold">Current Password</label>
                  <input type="password" class="form-control" id="current_password"
                    v-model="passwordForm.current_password" :class="{ 'is-invalid': passwordErrors.current_password }">
                  <div class="invalid-feedback" v-if="passwordErrors.current_password">{{
                    passwordErrors.current_password }}</div>
                </div>
                <div class="col-md-12">
                  <label for="new_password" class="form-label fw-semibold">New Password</label>
                  <input type="password" class="form-control" id="new_password" v-model="passwordForm.new_password"
                    :class="{ 'is-invalid': passwordErrors.new_password }">
                  <div class="invalid-feedback" v-if="passwordErrors.new_password">{{ passwordErrors.new_password }}
                  </div>
                </div>
                <div class="col-md-12">
                  <label for="password_confirmation" class="form-label fw-semibold">Confirm New Password</label>
                  <input type="password" class="form-control" id="password_confirmation"
                    v-model="passwordForm.password_confirmation"
                    :class="{ 'is-invalid': passwordErrors.password_confirmation }">
                  <div class="invalid-feedback" v-if="passwordErrors.password_confirmation">{{
                    passwordErrors.password_confirmation }}</div>
                </div>
                <div class="col-md-12">
                  <button type="button" class="btn btn-primary bg-primary-linear w-100" :disabled="passwordLoading"
                    @click="changePassword">
                    <span v-if="passwordLoading" class="spinner-border spinner-border-sm me-2"></span><i
                      class="bi bi-shield-check me-2"></i>Change Password
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-12">
          <div class="p-1">
              <button class="btn btn-outline-primary fw-bold w-100" @click="confirmDeleteAccount"><i class="bi bi-trash me-2"></i>Delete Account</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { mapState } from 'pinia'
import { useAuthStore } from '../stores/auth'
import { showToast } from '../utils/ui-toasts'

export default {
  name: 'Profile',
  computed: {
    ...mapState(useAuthStore, ['user']),
  },
  data() {
    return {
      loading: false,
      passwordLoading: false,
      profileImage: `https://ui-avatars.com/api/?name=${this.user?.name || 'User'}&background=0d6efd&color=fff&size=150`,
      form: {
        name: this.user?.name || '',
        email: this.user?.email || '',
        currency: this.user?.currency || 'USD'
      },
      passwordForm: {
        current_password: '',
        new_password: '',
        password_confirmation: ''
      },
      errors: {},
      passwordErrors: {}
    }
  },
  watch: {
    user(newVal) {
      this.profileImage = `https://ui-avatars.com/api/?name=${newVal?.name || 'User'}&background=0d6efd&color=fff&size=150`;
      this.form.name = newVal?.name || '';
      this.form.email = newVal?.email || '';
      this.form.currency = newVal?.currency || 'USD';
    }
  },
  methods: {
    async updateProfile() {
      this.loading = true;
      this.errors = {};

      try {
        const response = await this.$axios.put('/api/profile', this.form);

        if (response.data.success) {
          useAuthStore().setUser(response.data.user)
          showToast('Profile updated successfully!', 'success');
        } else {
          showToast(response.data.message || 'Failed to update profile', 'error');
        }
      } catch (error) {
        showToast('Failed to update profile', 'error');
      } finally {
        this.loading = false;
      }
    },

    async changePassword() {
      this.passwordLoading = true;
      this.passwordErrors = {};

      try {
        const response = await this.$axios.put('/api/change-password', this.passwordForm);

        if (response.data.success) {
          this.passwordForm = {
            current_password: '',
            new_password: '',
            password_confirmation: ''
          };
          showToast('Password changed successfully!', 'success');
        } else {
          showToast(response.data.message || 'Failed to change password', 'error');
        }
      } catch (error) {
        showToast('Failed to change password', 'error');
      } finally {
        this.passwordLoading = false;
      }
    },

    resetForm() {
      this.form = {
        name: this.user?.name || '',
        email: this.user?.email || '',
        currency: this.user?.currency || 'USD',
      };
      this.errors = {};
    },

    confirmDeleteAccount() {
      if (confirm('Are you sure you want to delete your account? This action cannot be undone.')) {
        showToast('Account deletion feature coming soon!', 'error');
      }
    },

    formatDate(date) {
      if (!date) return 'N/A';
      return new Date(date).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
      });
    },
  }
}
</script>
