<template>
  <div class="main-container">
    <!-- Modern Navigation Header -->
    <header class="main-header shadow-sm rounded-1">
      <nav class="navbar navbar-expand-lg">
        <div class="container-fluid">
          <!-- Brand/Logo -->
          <router-link class="navbar-brand fw-bold text-white" to="/">
            <i class="fas fa-store me-2 text-light"></i>
            <span class="brand-text">VueShop</span>
          </router-link>
          
          <!-- Mobile Toggle Button -->
          <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
          </button>
          
          <!-- Navigation Links -->
          <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
              <li class="nav-item">
                <router-link class="nav-link text-white" to="/" exact-active-class="active">
                  <i class="fas fa-home me-1"></i>Home
                </router-link>
              </li>
              <li v-if="isAuthenticated && isAdmin" class="nav-item">
                <router-link class="nav-link text-white" to="/users" active-class="active">
                  <i class="fas fa-users me-1"></i>Users
                </router-link>
              </li>
              <li v-if="isAuthenticated && isAdmin" class="nav-item">
                <router-link class="nav-link text-white" to="/products" active-class="active">
                  <i class="fas fa-box me-1"></i>Products
                </router-link>
              </li>
              <li v-if="isAuthenticated && isAdmin" class="nav-item">
                <router-link class="nav-link text-white" to="/categories" active-class="active">
                  <i class="fas fa-tags me-1"></i>Categories
                </router-link>
              </li>
              <li v-if="isAuthenticated && isAdmin" class="nav-item">
                <router-link class="nav-link text-white" to="/orders" active-class="active">
                  <i class="fas fa-box-open me-1"></i>Orders
                </router-link>
              </li>
              <li v-if="isAuthenticated && !isAdmin" class="nav-item">
                <router-link class="nav-link text-white" to="/product" active-class="active">
                  <i class="fas fa-shopping-bag me-1"></i>Products
                </router-link>
              </li>
              <li v-if="isAuthenticated && !isAdmin" class="nav-item">
                <router-link class="nav-link text-white position-relative" to="/my-cart" active-class="active">
                  <i class="fas fa-shopping-cart me-1"></i>My Cart
                </router-link>
              </li>
              <li v-if="isAuthenticated && !isAdmin" class="nav-item">
                <router-link class="nav-link text-white position-relative" to="/my-wishlist" active-class="active">
                  <i class="fa fa-bookmark me-1"></i>My Wishlist
                </router-link>
              </li>
              <li v-if="isAuthenticated && !isAdmin" class="nav-item">
                <router-link class="nav-link text-white position-relative" to="/my-orders" active-class="active">
                  <i class="fa fa-box-open me-1"></i>My Orders
                </router-link>
              </li>
            </ul>
            
            <!-- User Actions -->
            <div class="user-actions">
              <template v-if="isAuthenticated">
                <div class="user-profile-dropdown">
                  <button class="btn btn-link text-white text-decoration-none dropdown-toggle" type="button" data-bs-toggle="dropdown" data-bs-boundary="viewport">
                    <img :src="profileImage" alt="Profile" class="profile-image rounded-circle me-2">
                    <span class="user-name d-none d-md-inline">{{ user?.name }}</span>
                  </button>
                  <ul class="dropdown-menu dropdown-menu-end">
                    <li>
                      <router-link class="dropdown-item" to="/profile">
                        <i class="fas fa-user me-2"></i>Profile
                      </router-link>
                    </li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                      <button class="dropdown-item text-danger" type="button" @click="handleLogout">
                        <i class="fas fa-sign-out-alt me-2"></i>Logout
                      </button>
                    </li>
                  </ul>
                </div>
              </template>
              <template v-else>
                <router-link class="btn btn-outline-light me-2" to="/login">
                  <i class="fas fa-sign-in-alt me-1"></i>Login
                </router-link>
                <router-link class="btn btn-primary" to="/register">
                  <i class="fas fa-user-plus me-1"></i>Register
                </router-link>
              </template>
            </div>
          </div>
        </div>
      </nav>
    </header>
    
    <!-- Main Content -->
    <main class="main-content">
      <div class="card border-0 rounded-1">
        <router-view />
      </div>
    </main>
  </div>
</template>

<script>
import { mapState, mapActions } from 'pinia'
import { useAuthStore } from '../stores/auth'

export default {
  name: 'Main',
  computed: {
    ...mapState(useAuthStore, ['user', 'isAuthenticated', 'isAdmin']),
    profileImage() {
      return `https://ui-avatars.com/api/?name=${this.user?.name || 'User'}&background=0d6efd&color=fff&size=150`
    },
  },
  mounted() {
    this.fetchUser()
  },
  methods: {
    ...mapActions(useAuthStore, ['fetchUser', 'logout']),
    async handleLogout() {
      try {
        await this.logout()
        localStorage.removeItem('user')
        window.location.href = '/vue-demo/public/login'
      } catch (error) {
        console.error('Logout error:', error)
      }
    },
  },
}
</script>
