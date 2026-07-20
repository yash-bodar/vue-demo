<template>
  <div class="auth-page-bg">
    <!-- Animated Blobs -->
    <div class="auth-bg-blob auth-bg-blob-1"></div>
    <div class="auth-bg-blob auth-bg-blob-2"></div>

    <div class="auth-card-container" style="max-width: 900px;">
      <div class="auth-glass-card">
        <div class="row g-0">
          <!-- Left Side - Brand Banner -->
          <div class="col-lg-6 d-none d-lg-flex auth-left-banner text-center text-white">
            <div class="auth-logo-badge">
              <i class="fas fa-key"></i>
            </div>
            <h2 class="fw-bold mb-3">Forgot Password?</h2>
            <p class="text-white-50 px-4 mb-4">
              Don't worry! Just enter your email and we'll send you a secure link to reset your password.
            </p>
            <img :src="'images/login-page-bg-image.png'" class="card-img-login" alt="Background Graphic">
          </div>
          
          <!-- Right Side - Forgot Password Form -->
          <div class="col-lg-6 auth-right-form">
            <div class="d-flex flex-column h-100 justify-content-center">
              <div class="mb-4">
                <h3 class="auth-title-gradient mb-2">Recover Password</h3>
                <p class="text-muted">Enter your email to receive a password reset link</p>
              </div>

              <div v-if="error" class="alert-premium-error mb-4">
                <i class="fas fa-exclamation-circle me-2"></i>{{ error }}
              </div>

              <div v-if="successMessage" class="alert-premium-success mb-4">
                <i class="fas fa-check-circle me-2"></i>{{ successMessage }}
              </div>

              <form @submit.prevent="submitForm">
                <div class="mb-4">
                  <label for="email" class="premium-label">Email Address</label>
                  <div class="premium-input-group">
                    <input 
                      v-model="email" 
                      type="email" 
                      class="premium-input" 
                      id="email" 
                      placeholder="name@example.com" 
                      required
                    >
                    <i class="fas fa-envelope input-icon"></i>
                  </div>
                </div>

                <button 
                  type="submit" 
                  class="btn auth-btn-glow w-100 mb-3" 
                  :disabled="loading"
                >
                  <span v-if="loading"><i class="fas fa-circle-notch fa-spin me-2"></i>Sending link...</span>
                  <span v-else><i class="fas fa-paper-plane me-2"></i>Send Reset Link</span>
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
  name: 'ForgotPassword',
  data() {
    return {
      email: '',
      loading: false,
      error: '',
      successMessage: ''
    }
  },
  methods: {
    async submitForm() {
      this.loading = true;
      this.error = '';
      this.successMessage = '';
      try {
        const response = await this.$axios.post('/forgot-password', {
          email: this.email
        });
        this.successMessage = response.data.message;
        this.email = '';
      } catch (error) {
        this.error = error.response?.data?.message || 'Failed to request reset link';
        console.error('Forgot password error:', error);
      } finally {
        this.loading = false;
      }
    }
  }
}
</script>
