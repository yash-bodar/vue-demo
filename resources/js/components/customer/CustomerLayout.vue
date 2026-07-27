<template>
  <div class="customer-wrapper d-flex flex-column min-vh-100">
    <!-- Top Bar Notice -->
    <div class="bg-primary text-white py-1.5 px-3 fs-8 text-center fw-medium">
      <i class="fas fa-truck me-2"></i> Free Express Shipping on Orders Over $99 | Use Code <span class="fw-bold text-warning">FREESHIP</span>
    </div>

    <!-- Main Sticky Customer Header -->
    <header class="customer-header">
      <div class="container-xl">
        <div class="d-flex align-items-center justify-content-between py-2.5">
          <!-- Logo & Mobile Drawer Trigger -->
          <div class="d-flex align-items-center gap-3">
            <button class="btn btn-sm btn-icon border-0 text-dark d-lg-none" @click="isMobileDrawerOpen = true">
              <i class="fas fa-bars fs-4"></i>
            </button>
            
            <router-link to="/" class="d-flex align-items-center gap-2 text-decoration-none">
              <i class="fas fa-bag-shopping fs-2 text-primary"></i>
              <span class="fw-bold fs-3 text-heading letter-spacing-tight">Vuexy<span class="text-primary">Shop</span></span>
            </router-link>
          </div>

          <!-- Desktop Navigation -->
          <nav class="d-none d-lg-flex align-items-center gap-1">
            <router-link to="/" class="customer-nav-link" active-class="active" exact>
              Home
            </router-link>
            
            <router-link to="/product" class="customer-nav-link" active-class="active">
              Shop Store
            </router-link>

            <!-- Categories Dropdown -->
            <div class="dropdown">
              <button class="btn customer-nav-link dropdown-toggle border-0" data-bs-toggle="dropdown">
                Categories
              </button>
              <ul class="dropdown-menu shadow-lg border-0 rounded-3 p-2 mt-2" style="min-width: 200px;">
                <li v-for="cat in categories" :key="cat.id">
                  <router-link :to="{ path: '/product', query: { category: cat.id } }" class="dropdown-item rounded-2 py-2 fs-7">
                    <i class="fas fa-tag me-2 text-primary fs-8"></i>{{ cat.name }}
                  </router-link>
                </li>
                <li><hr class="dropdown-divider"></li>
                <li>
                  <router-link to="/product" class="dropdown-item rounded-2 py-2 fs-7 text-primary fw-semibold">
                    Browse All Categories <i class="fas fa-arrow-right ms-1 fs-8"></i>
                  </router-link>
                </li>
              </ul>
            </div>

            <router-link v-if="isAuthenticated" to="/my-orders" class="customer-nav-link" active-class="active">
              My Orders
            </router-link>
          </nav>

          <!-- Search Bar (Desktop) -->
          <div class="d-none d-md-flex align-items-center mx-3 flex-grow-1" style="max-width: 320px;">
            <div class="input-group">
              <input 
                type="text" 
                v-model="searchQuery" 
                @keyup.enter="handleSearch" 
                class="form-control form-control-sm rounded-start-pill border-end-0 ps-3" 
                placeholder="Search products..." 
              />
              <button class="btn btn-sm btn-primary rounded-end-pill px-3" @click="handleSearch">
                <i class="fas fa-search"></i>
              </button>
            </div>
          </div>

          <!-- Right Icons (Wishlist, Cart, Profile, Theme) -->
          <div class="d-flex align-items-center gap-2">
            <!-- Light/Dark Mode Switcher -->
            <button class="btn btn-sm btn-label-secondary rounded-circle btn-icon border-0" @click="toggleTheme" title="Toggle Theme">
              <i :class="isDarkMode ? 'fas fa-sun text-warning' : 'fas fa-moon text-primary'"></i>
            </button>

            <!-- Wishlist Icon -->
            <router-link to="/my-wishlist" class="btn btn-sm btn-label-secondary rounded-circle btn-icon border-0 position-relative" title="Wishlist">
              <i class="far fa-heart fs-6"></i>
              <span v-if="wishlistCount > 0" class="badge-counter bg-danger text-white">{{ wishlistCount }}</span>
            </router-link>

            <!-- Cart Icon -->
            <router-link to="/my-cart" class="btn btn-sm btn-primary rounded-pill px-3 d-flex align-items-center gap-2 position-relative" title="Shopping Cart">
              <i class="fas fa-cart-shopping fs-6"></i>
              <span class="d-none d-sm-inline fw-semibold fs-7">{{ formatCurrency(cartTotal) }}</span>
              <span v-if="cartCount > 0" class="badge rounded-pill bg-white text-primary fw-bold fs-9 ms-1">{{ cartCount }}</span>
            </router-link>

            <!-- User Dropdown / Auth Links -->
            <div class="dropdown ms-1">
              <template v-if="isAuthenticated">
                <button class="btn p-0 border-0 d-flex align-items-center gap-2" data-bs-toggle="dropdown">
                  <img :src="profileImage" class="rounded-circle border" width="38" height="38" alt="Avatar">
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 rounded-3 p-2 mt-2" style="min-width: 220px;">
                  <li class="px-3 py-2 border-bottom mb-1">
                    <div class="fw-bold">{{ user?.name }}</div>
                    <div class="fs-8 text-muted">{{ user?.email }}</div>
                  </li>
                  <li>
                    <router-link to="/profile" class="dropdown-item rounded-2 py-2">
                      <i class="fas fa-user me-2 text-primary"></i>My Profile
                    </router-link>
                  </li>
                  <li>
                    <router-link to="/my-orders" class="dropdown-item rounded-2 py-2">
                      <i class="fas fa-box-open me-2 text-success"></i>My Orders
                    </router-link>
                  </li>
                  <li>
                    <router-link to="/my-wishlist" class="dropdown-item rounded-2 py-2">
                      <i class="fas fa-heart me-2 text-danger"></i>Wishlist
                    </router-link>
                  </li>
                  <li><hr class="dropdown-divider"></li>
                  <li>
                    <button class="dropdown-item rounded-2 py-2 text-danger" @click="handleLogout">
                      <i class="fas fa-sign-out-alt me-2"></i>Logout
                    </button>
                  </li>
                </ul>
              </template>
              <template v-else>
                <div class="d-none d-sm-flex align-items-center gap-2">
                  <router-link to="/login" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                    Login
                  </router-link>
                  <router-link to="/register" class="btn btn-sm btn-primary rounded-pill px-3">
                    Register
                  </router-link>
                </div>
                <router-link to="/login" class="btn btn-sm btn-label-secondary rounded-circle btn-icon border-0 d-sm-none">
                  <i class="fas fa-user"></i>
                </router-link>
              </template>
            </div>
          </div>
        </div>
      </div>
    </header>

    <!-- Mobile Slide Drawer -->
    <div v-if="isMobileDrawerOpen" class="modal-backdrop fade show" style="z-index: 1040;" @click="isMobileDrawerOpen = false"></div>
    <div 
      class="position-fixed top-0 bottom-0 start-0 bg-card shadow-lg p-4 transition-smooth d-lg-none" 
      :style="{ width: '300px', zIndex: 1050, transform: isMobileDrawerOpen ? 'translateX(0)' : 'translateX(-100%)' }"
    >
      <div class="d-flex align-items-center justify-content-between border-bottom pb-3 mb-3">
        <span class="fw-bold fs-4 text-heading">Vuexy<span class="text-primary">Shop</span></span>
        <button class="btn btn-sm btn-icon border-0 text-muted" @click="isMobileDrawerOpen = false"><i class="fas fa-times fs-5"></i></button>
      </div>
      <div class="mb-4">
        <input 
          type="text" 
          v-model="searchQuery" 
          @keyup.enter="handleSearchMobile" 
          class="form-control rounded-pill" 
          placeholder="Search products..." 
        />
      </div>
      <div class="d-flex flex-column gap-2">
        <router-link to="/" class="btn text-start btn-light rounded-3 py-2" @click="isMobileDrawerOpen = false">
          <i class="fas fa-home me-2 text-primary"></i> Home
        </router-link>
        <router-link to="/product" class="btn text-start btn-light rounded-3 py-2" @click="isMobileDrawerOpen = false">
          <i class="fas fa-store me-2 text-info"></i> Shop Store
        </router-link>
        <router-link v-if="isAuthenticated" to="/my-orders" class="btn text-start btn-light rounded-3 py-2" @click="isMobileDrawerOpen = false">
          <i class="fas fa-box me-2 text-success"></i> My Orders
        </router-link>
        <router-link v-if="isAuthenticated" to="/my-wishlist" class="btn text-start btn-light rounded-3 py-2" @click="isMobileDrawerOpen = false">
          <i class="fas fa-heart me-2 text-danger"></i> My Wishlist
        </router-link>
        <router-link v-if="isAuthenticated" to="/profile" class="btn text-start btn-light rounded-3 py-2" @click="isMobileDrawerOpen = false">
          <i class="fas fa-user-gear me-2 text-warning"></i> Profile
        </router-link>
      </div>

      <div class="mt-auto pt-4 border-top">
        <template v-if="!isAuthenticated">
          <router-link to="/login" class="btn btn-outline-primary w-100 mb-2 rounded-pill" @click="isMobileDrawerOpen = false">Login</router-link>
          <router-link to="/register" class="btn btn-primary w-100 rounded-pill" @click="isMobileDrawerOpen = false">Register</router-link>
        </template>
        <template v-else>
          <button class="btn btn-danger w-100 rounded-pill" @click="handleLogout"><i class="fas fa-sign-out-alt me-2"></i> Logout</button>
        </template>
      </div>
    </div>

    <!-- Main Page Content Slot -->
    <main class="flex-grow-1">
      <slot></slot>
    </main>

    <!-- Customer Footer -->
    <footer class="customer-footer">
      <div class="container-xl">
        <div class="row g-4 mb-5">
          <!-- Company Info -->
          <div class="col-lg-4 col-md-6">
            <div class="d-flex align-items-center gap-2 mb-3">
              <i class="fas fa-bag-shopping fs-3 text-primary"></i>
              <span class="fw-bold fs-3 text-white">Vuexy<span class="text-primary">Shop</span></span>
            </div>
            <p class="fs-7 text-muted mb-4">
              Modern eCommerce platform engineered for luxury shopping, instant checkout, and premium fashion collections. Inspired by Vuexy design system.
            </p>
            <div class="d-flex gap-2">
              <a href="#" class="btn btn-sm btn-label-secondary rounded-circle btn-icon"><i class="fab fa-facebook-f"></i></a>
              <a href="#" class="btn btn-sm btn-label-secondary rounded-circle btn-icon"><i class="fab fa-twitter"></i></a>
              <a href="#" class="btn btn-sm btn-label-secondary rounded-circle btn-icon"><i class="fab fa-instagram"></i></a>
              <a href="#" class="btn btn-sm btn-label-secondary rounded-circle btn-icon"><i class="fab fa-github"></i></a>
            </div>
          </div>

          <!-- Categories Shortcut -->
          <div class="col-lg-2 col-md-6 col-6">
            <h5 class="fw-bold mb-3">Categories</h5>
            <ul class="list-unstyled fs-7 d-flex flex-column gap-2">
              <li v-for="cat in categories.slice(0, 5)" :key="cat.id">
                <router-link :to="{ path: '/product', query: { category: cat.id } }">{{ cat.name }}</router-link>
              </li>
            </ul>
          </div>

          <!-- Quick Links -->
          <div class="col-lg-2 col-md-6 col-6">
            <h5 class="fw-bold mb-3">Quick Links</h5>
            <ul class="list-unstyled fs-7 d-flex flex-column gap-2">
              <li><router-link to="/product">Shop All</router-link></li>
              <li><router-link to="/my-cart">View Cart</router-link></li>
              <li><router-link to="/my-wishlist">My Wishlist</router-link></li>
              <li><router-link to="/my-orders">Track Orders</router-link></li>
            </ul>
          </div>

          <!-- Newsletter Box -->
          <div class="col-lg-4 col-md-6">
            <h5 class="fw-bold mb-3">Subscribe to Newsletter</h5>
            <p class="fs-7 text-muted mb-3">Get $20 off your first order plus weekly exclusive discount drops!</p>
            <form @submit.prevent="handleNewsletter" class="input-group mb-3">
              <input type="email" v-model="newsletterEmail" class="form-control form-control-sm rounded-start-pill ps-3" placeholder="Enter your email..." required />
              <button class="btn btn-primary btn-sm rounded-end-pill px-4" type="submit">Join</button>
            </form>
            <div class="fs-8 text-muted d-flex align-items-center gap-2">
              <i class="fas fa-shield-halved text-success"></i> 100% Secure & No Spam Guaranteed
            </div>
          </div>
        </div>

        <div class="border-top border-secondary pt-4 d-flex flex-column flex-md-row align-items-center justify-content-between fs-8">
          <div>© {{ new Date().getFullYear() }} VuexyShop. All rights reserved.</div>
          <div class="d-flex gap-3 fs-5 text-muted mt-2 mt-md-0">
            <i class="fab fa-cc-visa"></i>
            <i class="fab fa-cc-mastercard"></i>
            <i class="fab fa-cc-stripe"></i>
            <i class="fab fa-cc-apple-pay"></i>
          </div>
        </div>
      </div>
    </footer>
  </div>
</template>

<script>
import { mapState, mapActions } from 'pinia'
import { useAuthStore } from '../../stores/auth'
import { useCartStore } from '../../stores/cart'
import { useWishlistStore } from '../../stores/wishlist'

export default {
  name: 'CustomerLayout',
  data() {
    return {
      searchQuery: '',
      newsletterEmail: '',
      isMobileDrawerOpen: false,
      isDarkMode: false,
      categories: [],
    }
  },
  computed: {
    ...mapState(useAuthStore, ['user', 'isAuthenticated']),
    ...mapState(useCartStore, ['items', 'totalCount']),
    ...mapState(useWishlistStore, { wishlistItems: 'items' }),
    profileImage() {
      return `https://ui-avatars.com/api/?name=${encodeURIComponent(this.user?.name || 'Customer')}&background=7367f0&color=fff&size=150`
    },
    cartCount() {
      const cartStore = useCartStore()
      return cartStore.items.reduce((sum, i) => sum + (i.quantity || 1), 0)
    },
    cartTotal() {
      const cartStore = useCartStore()
      return cartStore.items.reduce((sum, i) => sum + ((parseFloat(i.price) || 0) * (i.quantity || 1)), 0)
    },
    wishlistCount() {
      const wishlistStore = useWishlistStore()
      return wishlistStore.items.length
    }
  },
  async mounted() {
    const savedTheme = localStorage.getItem('theme') || 'light'
    this.isDarkMode = savedTheme === 'dark'
    document.documentElement.setAttribute('data-theme', savedTheme)

    await this.fetchCategories()
    const cartStore = useCartStore()
    if (cartStore.fetchCart) await cartStore.fetchCart()
    const wishlistStore = useWishlistStore()
    if (wishlistStore.fetchWishlist) await wishlistStore.fetchWishlist()
  },
  methods: {
    ...mapActions(useAuthStore, ['logout']),
    async fetchCategories() {
      try {
        const response = await this.$axios.get('/api/categories')
        this.categories = response.data.categories || response.data.data || response.data || []
      } catch (err) {
        console.error('Failed to fetch header categories', err)
      }
    },
    toggleTheme() {
      this.isDarkMode = !this.isDarkMode
      const theme = this.isDarkMode ? 'dark' : 'light'
      document.documentElement.setAttribute('data-theme', theme)
      localStorage.setItem('theme', theme)
    },
    handleSearch() {
      if (this.searchQuery.trim()) {
        this.$router.push({ path: '/product', query: { search: this.searchQuery.trim() } })
      }
    },
    handleSearchMobile() {
      this.handleSearch()
      this.isMobileDrawerOpen = false
    },
    handleNewsletter() {
      alert(`Thank you for subscribing with ${this.newsletterEmail}! Check your inbox for your $20 promo code.`)
      this.newsletterEmail = ''
    },
    formatCurrency(amount) {
      return `$${parseFloat(amount || 0).toFixed(2)}`
    },
    async handleLogout() {
      try {
        await this.logout()
        localStorage.removeItem('user')
        window.location.href = '/vue-demo/public/login'
      } catch (error) {
        console.error('Logout error:', error)
      }
    }
  }
}
</script>
