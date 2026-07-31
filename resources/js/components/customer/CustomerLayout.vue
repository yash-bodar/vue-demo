<template>
  <div class="customer-wrapper d-flex flex-column min-vh-100">
    <!-- Top Announcement Ribbon -->
    <div class="top-banner-ribbon d-flex align-items-center justify-content-between px-4">
      <div class="d-flex align-items-center gap-3">
        <span
          ><i class="fas fa-bolt text-warning me-1"></i> Special Launch Deal:
          <strong>20% OFF</strong> First Order</span
        >
        <span class="d-none d-md-inline opacity-75">|</span>
        <span class="d-none d-md-inline"
          ><i class="fas fa-headset me-1"></i> Support 24/7: +1 (800) 555-0199</span
        >
      </div>
      <div class="d-flex align-items-center gap-3">
        <span class="badge bg-white text-dark fw-bold px-2 py-1 fs-9">USD $</span>
        <span class="d-none d-sm-inline opacity-75">|</span>
        <router-link
          to="/product"
          class="text-white text-decoration-none fw-semibold fs-9 d-none d-sm-inline"
        >
          <i class="fas fa-location-dot me-1"></i> Track Order
        </router-link>
      </div>
    </div>

    <!-- Main Glassmorphism Customer Header -->
    <header class="customer-header">
      <div class="container-xl">
        <div class="d-flex align-items-center justify-content-between py-3">
          <!-- Logo & Mobile Drawer Trigger -->
          <div class="d-flex align-items-center gap-3">
            <button
              class="btn btn-sm btn-icon btn-label-secondary border-0 d-lg-none"
              @click="isMobileDrawerOpen = true"
            >
              <i class="fas fa-bars fs-4"></i>
            </button>

            <router-link to="/" class="d-flex align-items-center gap-2 text-decoration-none">
              <div class="badge bg-primary rounded-3 p-2 shadow-primary">
                <i class="fas fa-bag-shopping fs-4 text-white"></i>
              </div>
              <span class="fw-bold fs-3 text-heading letter-spacing-tight"
                >Vuexy<span class="text-primary">Shop</span></span
              >
            </router-link>

            <!-- Desktop Navigation Links -->
            <div class="d-none d-lg-flex align-items-center gap-2 ms-4">
              <router-link
                to="/"
                class="customer-nav-link"
                :class="{ active: $route.name === 'home' }"
              >
                Home
              </router-link>
              <router-link
                to="/product"
                class="customer-nav-link"
                :class="{ active: $route.name === 'product' || $route.name === 'product-detail' }"
              >
                Products
              </router-link>
            </div>
          </div>

          <!-- Central Search Pill Bar -->
          <div
            class="d-none d-md-flex align-items-center mx-4 flex-grow-1"
            style="max-width: 480px"
          >
            <div class="w-100 search-pill-wrapper d-flex align-items-center pe-1">
              <div class="ps-3 text-muted"><i class="fas fa-search"></i></div>
              <input
                type="text"
                v-model="searchQuery"
                @keyup.enter="handleSearch"
                class="form-control border-0 bg-transparent shadow-none fs-7 ps-2"
                placeholder="Search over 10,000+ luxury items..."
              />
              <button
                class="btn btn-sm btn-primary rounded-pill px-3 py-1.5 fw-bold"
                @click="handleSearch"
              >
                Search
              </button>
            </div>
          </div>

          <!-- Right Action Controls -->
          <div class="d-flex align-items-center gap-2">
            <!-- Light/Dark Mode Switcher -->
            <button
              class="btn btn-sm btn-label-secondary rounded-circle btn-icon border-0"
              @click="toggleTheme"
              title="Toggle Theme"
            >
              <i :class="isDarkMode ? 'fas fa-sun text-warning' : 'fas fa-moon text-primary'"></i>
            </button>

            <!-- Wishlist Icon Button -->
            <router-link
              to="/my-wishlist"
              class="btn btn-sm btn-label-secondary rounded-circle btn-icon border-0 position-relative"
              title="Wishlist"
            >
              <i class="far fa-heart fs-6"></i>
              <span v-if="wishlistCount > 0" class="badge-counter bg-danger text-white">{{
                wishlistCount
              }}</span>
            </router-link>

            <!-- Cart Pill Button -->
            <router-link
              to="/my-cart"
              class="btn btn-sm btn-primary rounded-pill px-3 py-2 d-flex align-items-center gap-2 position-relative shadow-primary"
              title="Shopping Cart"
            >
              <i class="fas fa-cart-shopping fs-6"></i>
              <span class="d-none d-sm-inline fw-bold fs-7">{{ formatCurrency(cartTotal) }}</span>
              <span
                v-if="cartCount > 0"
                class="badge rounded-pill bg-white text-primary fw-bold fs-9 ms-1"
                >{{ cartCount }}</span
              >
            </router-link>

            <!-- User Dropdown / Auth Buttons -->
            <div class="dropdown ms-1">
              <template v-if="isAuthenticated">
                <button
                  class="btn p-0 border-0 d-flex align-items-center gap-2"
                  data-bs-toggle="dropdown"
                >
                  <img
                    :src="profileImage"
                    class="rounded-circle border border-2 border-primary"
                    width="38"
                    height="38"
                    alt="Avatar"
                  />
                </button>
                <ul
                  class="dropdown-menu dropdown-menu-end shadow-lg border-0 rounded-3 p-2 mt-2 dropdown-menu-220"
                >
                  <li class="px-3 py-2 border-bottom mb-1">
                    <div class="fw-bold text-heading">{{ user?.name }}</div>
                    <div class="fs-8 text-muted">{{ user?.email }}</div>
                  </li>
                  <li>
                    <router-link to="/profile" class="dropdown-item rounded-2 py-2">
                      <i class="fas fa-user me-2 text-primary"></i>My Account
                    </router-link>
                  </li>
                  <li>
                    <router-link to="/my-orders" class="dropdown-item rounded-2 py-2">
                      <i class="fas fa-box-open me-2 text-success"></i>My Orders
                    </router-link>
                  </li>
                  <li>
                    <router-link to="/my-wishlist" class="dropdown-item rounded-2 py-2">
                      <i class="fas fa-heart me-2 text-danger"></i>My Wishlist
                    </router-link>
                  </li>
                  <li><hr class="dropdown-divider" /></li>
                  <li>
                    <button class="dropdown-item rounded-2 py-2 text-danger" @click="handleLogout">
                      <i class="fas fa-sign-out-alt me-2"></i>Sign Out
                    </button>
                  </li>
                </ul>
              </template>
              <template v-else>
                <div class="d-none d-sm-flex align-items-center gap-2 ms-2">
                  <router-link
                    to="/login"
                    class="btn btn-sm btn-outline-primary rounded-pill px-3 fw-bold"
                  >
                    Sign In
                  </router-link>
                  <router-link
                    to="/register"
                    class="btn btn-sm btn-primary rounded-pill px-3 fw-bold shadow-primary"
                  >
                    Join Free
                  </router-link>
                </div>
                <router-link
                  to="/login"
                  class="btn btn-sm btn-label-secondary rounded-circle btn-icon border-0 d-sm-none"
                >
                  <i class="fas fa-user"></i>
                </router-link>
              </template>
            </div>
          </div>
        </div>
      </div>
    </header>

    <!-- Mobile Slide Drawer -->
    <div
      v-if="isMobileDrawerOpen"
      class="modal-backdrop fade show z-index-drawer-backdrop"
      @click="isMobileDrawerOpen = false"
    ></div>
    <div
      class="position-fixed top-0 bottom-0 start-0 bg-card shadow-lg p-4 transition-smooth d-lg-none drawer-width-300 z-index-drawer"
      :class="{ 'translate-x-0': isMobileDrawerOpen, 'translate-x-minus-100': !isMobileDrawerOpen }"
    >
      <div class="d-flex align-items-center justify-content-between border-bottom pb-3 mb-3">
        <span class="fw-bold fs-4 text-heading">Vuexy<span class="text-primary">Shop</span></span>
        <button class="btn btn-sm btn-icon border-0 text-muted" @click="isMobileDrawerOpen = false">
          <i class="fas fa-times fs-5"></i>
        </button>
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
        <router-link
          to="/"
          class="btn text-start btn-light rounded-3 py-2"
          @click="isMobileDrawerOpen = false"
        >
          <i class="fas fa-home me-2 text-primary"></i> Home
        </router-link>
        <router-link
          to="/product"
          class="btn text-start btn-light rounded-3 py-2"
          @click="isMobileDrawerOpen = false"
        >
          <i class="fas fa-store me-2 text-info"></i> Shop Store
        </router-link>
        <router-link
          v-if="isAuthenticated"
          to="/my-orders"
          class="btn text-start btn-light rounded-3 py-2"
          @click="isMobileDrawerOpen = false"
        >
          <i class="fas fa-box me-2 text-success"></i> My Orders
        </router-link>
        <router-link
          v-if="isAuthenticated"
          to="/my-wishlist"
          class="btn text-start btn-light rounded-3 py-2"
          @click="isMobileDrawerOpen = false"
        >
          <i class="fas fa-heart me-2 text-danger"></i> My Wishlist
        </router-link>
        <router-link
          v-if="isAuthenticated"
          to="/profile"
          class="btn text-start btn-light rounded-3 py-2"
          @click="isMobileDrawerOpen = false"
        >
          <i class="fas fa-user-gear me-2 text-warning"></i> Profile
        </router-link>
      </div>

      <div class="mt-auto pt-4 border-top">
        <template v-if="!isAuthenticated">
          <router-link
            to="/login"
            class="btn btn-outline-primary w-100 mb-2 rounded-pill"
            @click="isMobileDrawerOpen = false"
            >Login</router-link
          >
          <router-link
            to="/register"
            class="btn btn-primary w-100 rounded-pill"
            @click="isMobileDrawerOpen = false"
            >Register</router-link
          >
        </template>
        <template v-else>
          <button class="btn btn-danger w-100 rounded-pill" @click="handleLogout">
            <i class="fas fa-sign-out-alt me-2"></i> Logout
          </button>
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
              <span class="fw-bold fs-3 text-white"
                >Vuexy<span class="text-primary">Shop</span></span
              >
            </div>
            <p class="fs-7 text-muted mb-4">
              Modern eCommerce platform engineered for luxury shopping, instant checkout, and
              premium fashion collections. Inspired by Vuexy design system.
            </p>
            <div class="d-flex gap-2">
              <a href="#" class="btn btn-sm btn-label-secondary rounded-circle btn-icon"
                ><i class="fab fa-facebook-f"></i
              ></a>
              <a href="#" class="btn btn-sm btn-label-secondary rounded-circle btn-icon"
                ><i class="fab fa-twitter"></i
              ></a>
              <a href="#" class="btn btn-sm btn-label-secondary rounded-circle btn-icon"
                ><i class="fab fa-instagram"></i
              ></a>
              <a href="#" class="btn btn-sm btn-label-secondary rounded-circle btn-icon"
                ><i class="fab fa-github"></i
              ></a>
            </div>
          </div>

          <!-- Categories Shortcut -->
          <div class="col-lg-2 col-md-6 col-6">
            <h5 class="fw-bold mb-3">Categories</h5>
            <ul class="list-unstyled fs-7 d-flex flex-column gap-2">
              <li v-for="cat in categories.slice(0, 5)" :key="cat.id">
                <router-link :to="{ path: '/product', query: { category: cat.id } }">{{
                  cat.name
                }}</router-link>
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
            <p class="fs-7 text-muted mb-3">
              Get $20 off your first order plus weekly exclusive discount drops!
            </p>
            <form @submit.prevent="handleNewsletter" class="input-group mb-3">
              <input
                type="email"
                v-model="newsletterEmail"
                class="form-control form-control-sm rounded-start-pill ps-3"
                placeholder="Enter your email..."
                required
              />
              <button class="btn btn-primary btn-sm rounded-end-pill px-4" type="submit">
                Join
              </button>
            </form>
            <div class="fs-8 text-muted d-flex align-items-center gap-2">
              <i class="fas fa-shield-halved text-success"></i> 100% Secure & No Spam Guaranteed
            </div>
          </div>
        </div>

        <div
          class="border-top border-secondary pt-4 d-flex flex-column flex-md-row align-items-center justify-content-between fs-8"
        >
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
      return cartStore.items.reduce(
        (sum, i) => sum + (parseFloat(i.price) || 0) * (i.quantity || 1),
        0
      )
    },
    wishlistCount() {
      const wishlistStore = useWishlistStore()
      return wishlistStore.items.length
    },
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
        const response = await this.$axios.get('/api/get-categories')
        this.categories = response.data.data?.data || response.data.data || response.data || []
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
      alert(
        `Thank you for subscribing with ${this.newsletterEmail}! Check your inbox for your $20 promo code.`
      )
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
    },
  },
}
</script>
