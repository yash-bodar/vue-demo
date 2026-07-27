<template>
  <div class="container-xl min-vh-100 d-flex align-items-center justify-content-center py-5">
    <div class="card card-vuexy overflow-hidden w-100 shadow-vuexy-lg" style="max-width: 900px;">
      <div class="row g-0">
        <!-- Left Banner -->
        <div class="col-lg-6 bg-primary text-white p-5 d-none d-lg-flex flex-column justify-content-between position-relative overflow-hidden">
          <div class="position-relative" style="z-index: 2;">
            <div class="d-flex align-items-center gap-2 mb-4">
              <i class="fas fa-cubes fs-2"></i>
              <span class="fw-bold fs-3 text-white">Vuexy App</span>
            </div>
            <h2 class="fw-bold text-white mb-3">Join Vuexy Today!</h2>
            <p class="text-white-50 fs-6">
              Create your account to start saving items, getting instant coupons, and experiencing luxury shopping.
            </p>
          </div>
          <div class="text-center position-relative" style="z-index: 2;">
            <i class="fas fa-user-plus fa-8x text-white-50 mb-3"></i>
            <div class="fs-8 text-white-50">Free & Fast Registration</div>
          </div>
        </div>

        <!-- Right Form -->
        <div class="col-lg-6 p-4 p-md-5 d-flex flex-column justify-content-center">
          <div class="mb-4">
            <h3 class="fw-bold text-heading mb-1">Create Account</h3>
            <p class="text-muted fs-7">Fill in your details to set up your account</p>
          </div>

          <div v-if="error" class="alert alert-danger shadow-sm rounded-3 fs-8 mb-4">
            <i class="fas fa-exclamation-circle me-2"></i>{{ error }}
          </div>

          <form @submit.prevent="submitForm">
            <div class="mb-3">
              <label class="form-label fs-7 fw-semibold">Full Name</label>
              <div class="input-group">
                <span class="input-group-text bg-transparent text-muted"><i class="fas fa-user fs-7"></i></span>
                <input v-model="form.name" type="text" class="form-control" placeholder="John Doe" required />
              </div>
            </div>

            <div class="mb-3">
              <label class="form-label fs-7 fw-semibold">Email Address</label>
              <div class="input-group">
                <span class="input-group-text bg-transparent text-muted"><i class="fas fa-envelope fs-7"></i></span>
                <input v-model="form.email" type="email" class="form-control" placeholder="name@example.com" required />
              </div>
            </div>

            <div class="mb-3">
              <label class="form-label fs-7 fw-semibold">Password</label>
              <div class="input-group">
                <span class="input-group-text bg-transparent text-muted"><i class="fas fa-lock fs-7"></i></span>
                <input v-model="form.password" :type="showPassword ? 'text' : 'password'" class="form-control" placeholder="••••••••" required />
                <button type="button" class="btn btn-outline-secondary" @click="showPassword = !showPassword">
                  <i :class="showPassword ? 'fas fa-eye-slash' : 'fas fa-eye'"></i>
                </button>
              </div>
            </div>

            <div class="mb-4">
              <label class="form-label fs-7 fw-semibold">Confirm Password</label>
              <div class="input-group">
                <span class="input-group-text bg-transparent text-muted"><i class="fas fa-lock fs-7"></i></span>
                <input v-model="form.password_confirmation" :type="showConfirmPassword ? 'text' : 'password'" class="form-control" placeholder="••••••••" required />
                <button type="button" class="btn btn-outline-secondary" @click="showConfirmPassword = !showConfirmPassword">
                  <i :class="showConfirmPassword ? 'fas fa-eye-slash' : 'fas fa-eye'"></i>
                </button>
              </div>
            </div>

            <button type="submit" class="btn btn-primary w-100 rounded-pill py-2.5 fw-bold shadow-primary mb-3" :disabled="loading">
              <span v-if="loading"><i class="fas fa-spinner fa-spin me-2"></i>Creating account...</span>
              <span v-else><i class="fas fa-user-plus me-2"></i>Create Account</span>
            </button>
          </form>

          <div class="text-center fs-7 text-muted mt-2">
            Already have an account? 
            <router-link to="/login" class="fw-bold text-primary">Login here</router-link>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'Register',
  data() {
    return {
      form: {
        name: '',
        email: '',
        password: '',
        password_confirmation: '',
      },
      showPassword: false,
      showConfirmPassword: false,
      loading: false,
      error: '',
    }
  },
  methods: {
    async submitForm() {
      this.loading = true
      this.error = ''
      try {
        const response = await this.$axios.post('/register', this.form)
        localStorage.setItem('user', JSON.stringify(response.data))
        window.location.href = '/vue-demo/public'
      } catch (error) {
        this.error = error.response?.data?.message || 'Registration failed'
        console.error('Register error:', error)
      } finally {
        this.loading = false
      }
    }
  }
}
</script>
