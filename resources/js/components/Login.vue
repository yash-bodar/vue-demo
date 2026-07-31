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
            <h2 class="fw-bold mb-3">Welcome to Vuexy Commerce</h2>
            <p class="text-white-50 px-4 mb-4">
              Experience next-generation eCommerce dashboard and customer portal built with modern
              design principles.
            </p>
            <img
              :src="'images/login-page-bg-image.png'"
              class="card-img-login"
              alt="Background Graphic"
            />
          </div>

          <!-- Right Side - Sign In Form -->
          <div class="col-lg-6 auth-right-form">
            <div class="d-flex flex-column h-100 justify-content-center">
              <div class="mb-4">
                <h3 class="auth-title-gradient mb-2">Sign In</h3>
                <p class="text-muted">Please enter your credentials to access your account</p>
              </div>

              <div v-if="error" class="alert-premium-error mb-4">
                <i class="fas fa-exclamation-circle me-2"></i>{{ error }}
              </div>

              <form @submit.prevent="submitForm">
                <div class="mb-3">
                  <label for="email" class="premium-label">Email Address</label>
                  <div class="premium-input-group">
                    <input
                      v-model="form.email"
                      type="email"
                      class="premium-input"
                      id="email"
                      placeholder="admin@demo.com"
                      required
                    />
                    <i class="fas fa-envelope input-icon"></i>
                  </div>
                </div>

                <div class="mb-4">
                  <div class="d-flex justify-content-between align-items-center mb-1">
                    <label for="password" class="premium-label mb-0">Password</label>
                    <router-link to="/forgot-password" class="text-link-premium fs-8"
                      >Forgot Password?</router-link
                    >
                  </div>
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

                <button type="submit" class="btn auth-btn-glow w-100 mb-3" :disabled="loading">
                  <span v-if="loading"
                    ><i class="fas fa-circle-notch fa-spin me-2"></i>Signing in...</span
                  >
                  <span v-else><i class="fas fa-sign-in-alt me-2"></i>Sign In</span>
                </button>
              </form>

              <div class="text-center mt-3">
                <p class="mb-0 text-muted">
                  Don't have an account?
                  <router-link to="/register" class="text-link-premium">Register here</router-link>
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
  name: 'Login',
  data() {
    return {
      form: {
        email: '',
        password: '',
      },
      showPassword: false,
      loading: false,
      error: '',
    }
  },
  methods: {
    async submitForm() {
      this.loading = true
      this.error = ''
      try {
        const response = await this.$axios.post('/login', {
          email: this.form.email,
          password: this.form.password,
        })
        localStorage.setItem('user', JSON.stringify(response.data))

        if (response.data.is_admin) {
          window.location.href = '/vue-demo/public/dashboard'
        } else {
          window.location.href = '/vue-demo/public'
        }
      } catch (error) {
        this.error = error.response?.data?.message || 'Login failed'
        console.error('Login error:', error)
      } finally {
        this.loading = false
      }
    },
  },
}
</script>
