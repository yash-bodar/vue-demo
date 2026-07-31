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
              <i class="fas fa-user-shield"></i>
            </div>
            <h2 class="fw-bold mb-3">Reset Password</h2>
            <p class="text-white-50 px-4 mb-4">
              Enter your new credentials below to secure your account and recover login access.
            </p>
            <img
              :src="'images/login-page-bg-image.png'"
              class="card-img-login"
              alt="Background Graphic"
            />
          </div>

          <!-- Right Side - Reset Password Form -->
          <div class="col-lg-6 auth-right-form">
            <div class="d-flex flex-column h-100 justify-content-center">
              <div class="mb-4">
                <h3 class="auth-title-gradient mb-2">New Password</h3>
                <p class="text-muted">Enter your new password details below</p>
              </div>

              <div v-if="error" class="alert-premium-error mb-4">
                <i class="fas fa-exclamation-circle me-2"></i>{{ error }}
              </div>

              <div v-if="successMessage" class="alert-premium-success mb-4">
                <i class="fas fa-check-circle me-2"></i>{{ successMessage }}
              </div>

              <form @submit.prevent="submitForm">
                <div class="mb-3">
                  <label for="password" class="premium-label">New Password</label>
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

                <button
                  type="submit"
                  class="btn auth-btn-glow w-100 mb-3"
                  :disabled="loading || !!error"
                >
                  <span v-if="loading"
                    ><i class="fas fa-circle-notch fa-spin me-2"></i>Resetting...</span
                  >
                  <span v-else><i class="fas fa-key me-2"></i>Update Password</span>
                </button>
              </form>

              <div class="text-center mt-3">
                <p class="mb-0 text-muted">
                  Remembered your password?
                  <router-link to="/login" class="text-link-premium">Back to Login</router-link>
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
  name: 'ResetPassword',
  data() {
    return {
      form: {
        token: '',
        email: '',
        password: '',
        password_confirmation: '',
      },
      showPassword: false,
      showConfirmPassword: false,
      loading: false,
      error: '',
      successMessage: '',
    }
  },
  created() {
    this.form.token = this.$route.query.token || ''
    this.form.email = this.$route.query.email || ''

    if (!this.form.token || !this.form.email) {
      this.error = 'Invalid reset password link. Please request a new one.'
    }
  },
  methods: {
    async submitForm() {
      if (this.form.password !== this.form.password_confirmation) {
        this.error = 'Passwords do not match'
        return
      }

      this.loading = true
      this.error = ''
      this.successMessage = ''
      try {
        const response = await this.$axios.post('/reset-password', this.form)
        this.successMessage = response.data.message
        this.form.password = ''
        this.form.password_confirmation = ''

        setTimeout(() => {
          this.$router.push('/login')
        }, 3000)
      } catch (error) {
        this.error = error.response?.data?.message || 'Failed to reset password'
        console.error('Reset password error:', error)
      } finally {
        this.loading = false
      }
    },
  },
}
</script>
