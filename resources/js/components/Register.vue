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

    <div class="col-lg-6">
      <div class="register-form auth-form h-100 d-flex align-items-center justify-content-center">
        <div class="w-100" style="max-width: 400px;">
          <div class="text-center mb-4">
            <h4 class="fw-bold text-primary">Register</h4>
            <p class="text-muted">Enter your credentials to create an account</p>
          </div>
          <div v-if="error" class="alert alert-danger">{{ error }}</div>
          <div class="mb-3">
            <label for="name" class="form-label fw-semibold"><i class="fas fa-user me-2 text-primary"></i>Name</label>
            <input v-model="form.name" class="form-control form-control-lg" id="name" required>
          </div>
          <div class="mb-3">
            <label for="email" class="form-label fw-semibold"><i class="fas fa-envelope me-2 text-primary"></i>Email</label>
            <input v-model="form.email" class="form-control form-control-lg" id="email" type="email" required>
          </div>
          <div class="mb-3">
            <label for="password" class="form-label fw-semibold"><i class="fas fa-lock me-2 text-primary"></i>Password</label>
            <input v-model="form.password" class="form-control form-control-lg" id="password" type="password" required>
          </div>
          <div class="mb-3">
            <label for="password_confirmation" class="form-label fw-semibold"><i class="fas fa-lock me-2 text-primary"></i>Confirm Password</label>
            <input v-model="form.password_confirmation" class="form-control form-control-lg" id="password_confirmation" type="password" required>
          </div>
          <div class="d-grid gap-2">
            <button type="button" class="btn btn-primary btn-lg bg-primary-linear" :disabled="loading" @click="submitForm">
              <span v-if="loading"><i class="fas fa-spinner fa-spin me-2"></i>Registering...</span>
              <span v-else><i class="fas fa-sign-in-alt me-2"></i>Register</span>
            </button>
          </div>
          <div class="text-center mt-4">
            <p class="mb-0 text-muted"> Already have an account? <router-link to="/login" class="text-decoration-none fw-semibold text-primary">Login</router-link></p>
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
            loading: false,
            error: '',
        }
    },
    methods: {
        async submitForm() {
            this.loading = true;
            this.error = '';
            // Validate password match
            if (this.form.password !== this.form.password_confirmation) {
                this.error = 'Passwords do not match';
                this.loading = false;
                return;
            }
            try {
                await this.$axios.post('/register', this.form);
                window.location.href = '/vue-demo/public';
            } catch (error) {
                console.log(error);
                this.error = error.response?.data?.message || 'Registration failed';
            } finally {
                this.loading = false;
            }
        }
    }
}
</script>

