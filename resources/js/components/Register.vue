<template>
  <div class="auth-page-bg">
    <!-- Animated Blobs -->
    <div class="auth-bg-blob auth-bg-blob-1"></div>
    <div class="auth-bg-blob auth-bg-blob-2"></div>

    <div class="auth-card-container auth-card-wrapper">
      <div class="auth-glass-card">
        <div class="row g-0">
          <!-- Left Side - Brand Banner -->
          <div class="col-lg-6 d-none d-lg-flex auth-left-banner text-center text-white">
            <div class="auth-logo-badge">
              <i class="fas fa-cubes"></i>
            </div>
            <h2 class="fw-bold mb-3">Join Vuexy Today!</h2>
            <p class="text-white-50 px-4 mb-4">
              Create your account to start saving items, getting instant coupons, and experiencing
              luxury shopping.
            </p>
            <img
              :src="'images/login-page-bg-image.png'"
              class="card-img-login"
              alt="Background Graphic"
            />
          </div>

          <!-- Right Side - Registration Form -->
          <div class="col-lg-6 auth-right-form">
            <div class="d-flex flex-column h-100 justify-content-center">
              <div class="mb-4">
                <h3 class="auth-title-gradient mb-2">Create Account</h3>
                <p class="text-muted">Fill in your details to set up your account</p>
              </div>

              <div v-if="error" class="alert-premium-error mb-4">
                <i class="fas fa-exclamation-circle me-2"></i>{{ error }}
              </div>

              <form @submit.prevent="submitForm">
                <div class="mb-3">
                  <label for="name" class="premium-label">Full Name</label>
                  <div class="premium-input-group">
                    <input
                      v-model="form.name"
                      type="text"
                      class="premium-input"
                      id="name"
                      placeholder="John Doe"
                      required
                    />
                    <i class="fas fa-user input-icon"></i>
                  </div>
                </div>

                <div class="mb-3">
                  <label for="email" class="premium-label">Email Address</label>
                  <div class="premium-input-group">
                    <input
                      v-model="form.email"
                      type="email"
                      class="premium-input"
                      id="email"
                      placeholder="name@example.com"
                      required
                    />
                    <i class="fas fa-envelope input-icon"></i>
                  </div>
                </div>

                <div class="mb-3">
                  <label for="password" class="premium-label">Password</label>
                  <div class="premium-input-group">
                    <input
                      v-model="form.password"
                      :type="showPassword ? 'text' : 'password'"
                      class="premium-input"
                      id="password"
                      placeholder="••••••••"
                      required
                    />
                    <i class="fas fa-lock input-icon"></i>
                    <button
                      type="button"
                      class="password-toggle-btn"
                      @click="showPassword = !showPassword"
                    >
                      <i :class="showPassword ? 'fas fa-eye-slash' : 'fas fa-eye'"></i>
                    </button>
                  </div>
                </div>

                <div class="mb-4">
                  <label for="password_confirmation" class="premium-label">Confirm Password</label>
                  <div class="premium-input-group">
                    <input
                      v-model="form.password_confirmation"
                      :type="showConfirmPassword ? 'text' : 'password'"
                      class="premium-input"
                      id="password_confirmation"
                      placeholder="••••••••"
                      required
                    />
                    <i class="fas fa-lock input-icon"></i>
                    <button
                      type="button"
                      class="password-toggle-btn"
                      @click="showConfirmPassword = !showConfirmPassword"
                    >
                      <i :class="showConfirmPassword ? 'fas fa-eye-slash' : 'fas fa-eye'"></i>
                    </button>
                  </div>
                </div>

                <button type="submit" class="btn auth-btn-glow w-100 mb-3" :disabled="loading">
                  <span v-if="loading"
                    ><i class="fas fa-circle-notch fa-spin me-2"></i>Creating account...</span
                  >
                  <span v-else><i class="fas fa-user-plus me-2"></i>Create Account</span>
                </button>
              </form>

              <div class="text-center mt-3">
                <p class="mb-0 text-muted">
                  Already have an account?
                  <router-link to="/login" class="text-link-premium">Login here</router-link>
                </p>
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
    },
  },
}
</script>
