<template>
  <div class="row g-0">
    <!-- Left Side - Image -->
    <div class="col-lg-6 d-none d-lg-block left-image-container">
      <div class="left-image h-100 d-flex align-items-center justify-content-center">
        <div class="text-center text-white p-4">
          <img :src="'images/login-page-bg-image.png'" class="card-img-login">
        </div>
      </div>
    </div>
    
    <!-- Right Side - Form -->
    <div class="col-lg-6">
      <div class="login-form auth-form h-100 d-flex align-items-center justify-content-center">
        <div class="w-100" style="max-width: 400px;">
          <div class="text-center mb-4">
            <h4 class="fw-bold text-primary">Login</h4>
            <p class="text-muted">Enter your credentials to access your account</p>
          </div>
          <div v-if="error" class="alert alert-danger">{{ error }}</div>
          <div class="mb-3">
            <label for="email" class="form-label fw-semibold"><i class="fas fa-envelope me-2 text-primary"></i>Email Address</label>
            <input v-model="form.email" class="form-control form-control-lg" id="email" type="email" placeholder="Enter your email" :class="{ 'is-invalid': error }" required>
          </div>
          <div class="mb-4">
            <label for="password" class="form-label fw-semibold"><i class="fas fa-lock me-2 text-primary"></i>Password</label>
            <input v-model="form.password" type="password" class="form-control form-control-lg" id="password" placeholder="Enter your password" required :class="{ 'is-invalid': error }">
          </div>
          <div class="d-grid gap-2">
            <button type="button" class="btn btn-primary btn-lg bg-primary-linear" :disabled="loading" @click="submitForm">
              <span v-if="loading"><i class="fas fa-spinner fa-spin me-2"></i>Logging in...</span>
              <span v-else><i class="fas fa-sign-in-alt me-2"></i>Login</span>
            </button>
          </div>
          <div class="text-center mt-4">
            <p class="mb-0 text-muted"> Don't have an account? <router-link to="/register" class="text-decoration-none fw-semibold text-primary">Register here</router-link></p>
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
      loading: false,
      error: ''
    }
  },
  methods: {
    async submitForm() {
      this.loading = true;
      this.error = '';
      try {
        const response = await this.$axios.post('/login', {
          email: this.form.email,
          password: this.form.password
        });
        localStorage.setItem('user', JSON.stringify(response.data));
        window.location.href = '/vue-demo/public';
      } catch (error) {
        this.error = error.response?.data?.message || 'Login failed';
        console.error('Login error:', error);
      } finally {
        this.loading = false;
      }
    }
  }
}
</script>