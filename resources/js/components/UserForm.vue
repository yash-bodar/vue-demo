<template>
  <div class="card bg-white shadow-sm border-0">
    <div class="card-header bg-gradient-primary text-light py-3 px-4 border-0 bg-primary-linear">
      <div class="row align-items-center">
        <div class="col">
          <h4 class="mb-0 fw-bold d-flex align-items-center">
            <i class="bi bi-person me-2"></i>
            {{ isEdit ? 'Edit User' : 'Create User' }}
          </h4>
        </div>
      </div>
    </div>
    <div class="card-body p-4">
      <form @submit.prevent="submitForm">
        <div class="row g-4">
          <!-- Left Column -->
          <div class="col-lg-8">
            <div class="mb-4">
              <label class="form-label fw-semibold">
                <i class="fas fa-user me-2 text-primary"></i> Name
              </label>
              <input
                v-model="form.name"
                class="form-control form-control-lg"
                placeholder="Enter user name"
                required
              />
            </div>

            <div class="mb-4">
              <label class="form-label fw-semibold">
                <i class="fas fa-envelope me-2 text-primary"></i> Email
              </label>
              <input
                v-model="form.email"
                class="form-control form-control-lg"
                type="email"
                placeholder="Enter email address"
                required
              />
            </div>

            <div class="row" v-if="!isEdit">
              <div class="col-md-6 mb-4">
                <label class="form-label fw-semibold">
                  <i class="fas fa-lock me-2 text-primary"></i> Password
                </label>
                <input
                  v-model="form.password"
                  type="password"
                  class="form-control"
                  placeholder="Enter password"
                  required
                />
              </div>

              <div class="col-md-6 mb-4">
                <label class="form-label fw-semibold">
                  <i class="fas fa-lock me-2 text-primary"></i> Confirm Password
                </label>
                <input
                  v-model="form.password_confirmation"
                  type="password"
                  class="form-control"
                  placeholder="Confirm password"
                  required
                />
              </div>
            </div>
          </div>

          <!-- Right Column -->
          <div class="col-lg-4">
            <div class="card bg-light border-0">
              <div class="card-body">
                <h6 class="card-title fw-bold mb-4">
                  <i class="fas fa-cog me-1 text-primary"></i> User Settings
                </h6>

                <div class="mb-4">
                  <label class="form-label fw-semibold">
                    <i class="fas fa-toggle-on me-2 text-primary"></i> Status
                  </label>
                  <select v-model="form.status" class="form-select" required>
                    <option value="Active">Active</option>
                    <option value="Inactive">Inactive</option>
                  </select>
                </div>

                <div class="mb-4">
                  <label class="form-label fw-semibold">
                    <i class="fas fa-money-bill-wave me-2 text-primary"></i> Currency
                  </label>
                  <select v-model="form.currency" class="form-select" required>
                    <option value="USD">USD - US Dollar ($)</option>
                    <option value="EUR">EUR - Euro (€)</option>
                    <option value="GBP">GBP - British Pound (£)</option>
                    <option value="CAD">CAD - Canadian Dollar (C$)</option>
                    <option value="AUD">AUD - Australian Dollar (A$)</option>
                    <option value="INR">INR - Indian Rupee (₹)</option>
                    <option value="AED">AED - UAE Dirham (د.إ)</option>
                  </select>
                </div>

                <div class="d-grid gap-2">
                  <router-link class="btn btn-outline-secondary" to="/users">
                    <i class="fas fa-arrow-left me-1"></i> Back to Users
                  </router-link>
                  <button class="btn btn-primary btn-lg bg-primary-linear" type="submit">
                    <i class="fas fa-check me-1"></i> {{ isEdit ? 'Update User' : 'Create User' }}
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </form>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      form: {
        id: null,
        name: '',
        email: '',
        password: '',
        password_confirmation: '',
        status: 'Active',
        currency: 'USD',
      },
      isEdit: false,
    }
  },
  mounted() {
    const id = this.$route.params.id
    console.log('UserForm mounted with id:', id)
    if (id) {
      this.isEdit = true
      this.$axios.get(`/api/users/${id}`).then((res) => {
        this.form = res.data.data
      })
    }
  },
  methods: {
    submitForm() {
      if (!this.isEdit) {
        if (this.form.password !== this.form.password_confirmation) {
          alert('Passwords do not match')
          return
        }
      }
      this.$axios.post('/api/users', this.form).then(() => this.$router.push('/users'))
    },
  },
}
</script>
