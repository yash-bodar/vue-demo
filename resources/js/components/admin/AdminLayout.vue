<template>
  <div class="vx-admin-wrapper" :class="{ 'vx-sidebar-collapsed': isCollapsed }">
    <!-- Mobile Backdrop -->
    <div
      v-if="isMobileOpen"
      class="modal-backdrop fade show d-lg-none z-index-backdrop"
      @click="isMobileOpen = false"
    ></div>

    <!-- Admin Collapsible Sidebar -->
    <aside class="vx-sidebar" :class="{ collapsed: isCollapsed, 'mobile-open': isMobileOpen }">
      <!-- Sidebar Header / Logo -->
      <div class="vx-sidebar-header">
        <router-link to="/dashboard" class="vx-sidebar-logo text-decoration-none">
          <i class="fas fa-cubes text-primary fs-3"></i>
          <span v-if="!isCollapsed" class="fw-bold">Vuexy Admin</span>
        </router-link>

        <!-- Collapse / Mobile Toggle Button -->
        <button
          class="btn btn-sm btn-icon text-muted d-none d-lg-inline-flex border-0"
          @click="toggleSidebar"
          :title="isCollapsed ? 'Expand Sidebar' : 'Collapse Sidebar'"
        >
          <i :class="isCollapsed ? 'fas fa-circle-dot' : 'far fa-circle'"></i>
        </button>
        <button
          class="btn btn-sm btn-icon text-muted d-lg-none border-0"
          @click="isMobileOpen = false"
        >
          <i class="fas fa-times"></i>
        </button>
      </div>

      <!-- Navigation Menu -->
      <div class="vx-sidebar-menu">
        <div class="vx-menu-header" v-if="!isCollapsed">Main Menu</div>

        <!-- Dashboard -->
        <router-link to="/dashboard" class="vx-menu-item" active-class="active">
          <span class="vx-menu-icon"><i class="fas fa-home"></i></span>
          <span class="vx-menu-text">Dashboard</span>
        </router-link>

        <!-- Catalog Accordion Header -->
        <div
          class="vx-menu-item d-flex justify-content-between"
          :class="{ active: isCatalogActive }"
          @click="toggleSubmenu('catalog')"
        >
          <div class="d-flex align-items-center">
            <span class="vx-menu-icon"><i class="fas fa-boxes-packing"></i></span>
            <span class="vx-menu-text">Catalog</span>
          </div>
          <i
            v-if="!isCollapsed"
            class="fas fa-chevron-down vx-chevron fs-7"
            :class="{ 'fa-rotate-180': openSubmenu === 'catalog' }"
          ></i>
        </div>
        <!-- Catalog Submenu -->
        <div v-show="openSubmenu === 'catalog' && !isCollapsed" class="vx-submenu animate-fade-in">
          <router-link to="/products" class="vx-menu-item" active-class="active">
            <span class="vx-menu-icon"><i class="fas fa-box"></i></span>
            <span class="vx-menu-text">Products</span>
          </router-link>
          <router-link to="/categories" class="vx-menu-item" active-class="active">
            <span class="vx-menu-icon"><i class="fas fa-tags"></i></span>
            <span class="vx-menu-text">Categories</span>
          </router-link>
          <router-link to="/colors" class="vx-menu-item" active-class="active">
            <span class="vx-menu-icon"><i class="fas fa-palette"></i></span>
            <span class="vx-menu-text">Colors</span>
          </router-link>
          <router-link to="/sizes" class="vx-menu-item" active-class="active">
            <span class="vx-menu-icon"><i class="fas fa-ruler-combined"></i></span>
            <span class="vx-menu-text">Sizes</span>
          </router-link>
        </div>

        <!-- Sales Accordion Header -->
        <div class="vx-menu-header" v-if="!isCollapsed">Sales & Marketing</div>

        <div
          class="vx-menu-item d-flex justify-content-between"
          :class="{ active: isSalesActive }"
          @click="toggleSubmenu('sales')"
        >
          <div class="d-flex align-items-center">
            <span class="vx-menu-icon"><i class="fas fa-chart-line"></i></span>
            <span class="vx-menu-text">Sales</span>
          </div>
          <i
            v-if="!isCollapsed"
            class="fas fa-chevron-down vx-chevron fs-7"
            :class="{ 'fa-rotate-180': openSubmenu === 'sales' }"
          ></i>
        </div>
        <!-- Sales Submenu -->
        <div v-show="openSubmenu === 'sales' && !isCollapsed" class="vx-submenu animate-fade-in">
          <router-link to="/orders" class="vx-menu-item" active-class="active">
            <span class="vx-menu-icon"><i class="fas fa-shopping-cart"></i></span>
            <span class="vx-menu-text">Orders</span>
          </router-link>
          <router-link to="/coupons" class="vx-menu-item" active-class="active">
            <span class="vx-menu-icon"><i class="fas fa-ticket-alt"></i></span>
            <span class="vx-menu-text">Coupons</span>
          </router-link>
        </div>

        <!-- User Management -->
        <div class="vx-menu-header" v-if="!isCollapsed">User Management</div>

        <router-link to="/users" class="vx-menu-item" active-class="active">
          <span class="vx-menu-icon"><i class="fas fa-users-gear"></i></span>
          <span class="vx-menu-text">Users</span>
        </router-link>

        <div class="vx-menu-header" v-if="!isCollapsed">System</div>

        <router-link to="/profile" class="vx-menu-item" active-class="active">
          <span class="vx-menu-icon"><i class="fas fa-user-cog"></i></span>
          <span class="vx-menu-text">Settings & Profile</span>
        </router-link>
      </div>

      <!-- User Profile & Logout at Bottom -->
      <div class="p-3 border-top border-subtle d-flex align-items-center justify-content-between">
        <div class="d-flex align-items-center gap-2 overflow-hidden" v-if="!isCollapsed">
          <img
            :src="profileImage"
            class="rounded-circle border"
            width="36"
            height="36"
            alt="Avatar"
          />
          <div class="text-truncate">
            <div class="fw-bold fs-7 text-truncate">{{ user?.name || 'Admin User' }}</div>
            <div class="text-muted fs-8">Administrator</div>
          </div>
        </div>
        <button
          class="btn btn-sm btn-icon text-danger border-0 ms-auto"
          @click="handleLogout"
          title="Logout"
        >
          <i class="fas fa-sign-out-alt fs-6"></i>
        </button>
      </div>
    </aside>

    <!-- Main Body Area -->
    <div class="vx-main-content-wrapper">
      <!-- Modern Floating Navbar -->
      <header class="vx-navbar">
        <!-- Left: Mobile Menu Toggle & Title / Breadcrumbs -->
        <div class="d-flex align-items-center gap-3">
          <button
            class="btn btn-sm btn-icon border-0 text-muted d-lg-none"
            @click="isMobileOpen = !isMobileOpen"
          >
            <i class="fas fa-bars fs-5"></i>
          </button>
          <div>
            <h5 class="m-0 fw-bold text-heading">{{ currentPageTitle }}</h5>
            <div class="fs-8 text-muted d-none d-sm-flex align-items-center gap-1">
              <span>Admin</span>
              <i class="fas fa-chevron-right fs-9"></i>
              <span class="text-primary fw-semibold">{{ currentPageTitle }}</span>
            </div>
          </div>
        </div>

        <!-- Right Action Controls -->
        <div class="d-flex align-items-center gap-2">
          <!-- Global Search trigger button -->
          <button
            class="btn btn-sm btn-label-secondary rounded-circle btn-icon border-0"
            @click="showSearchModal = true"
            title="Search (Ctrl + K)"
          >
            <i class="fas fa-search"></i>
          </button>

          <!-- Light / Dark Mode Toggle -->
          <button
            class="btn btn-sm btn-label-secondary rounded-circle btn-icon border-0"
            @click="toggleTheme"
            title="Toggle Theme"
          >
            <i :class="isDarkMode ? 'fas fa-sun text-warning' : 'fas fa-moon text-primary'"></i>
          </button>

          <!-- Fullscreen Toggle -->
          <button
            class="btn btn-sm btn-label-secondary rounded-circle btn-icon border-0 d-none d-sm-inline-flex"
            @click="toggleFullscreen"
            title="Fullscreen"
          >
            <i :class="isFullscreen ? 'fas fa-compress' : 'fas fa-expand'"></i>
          </button>

          <!-- Notifications Dropdown -->
          <div class="dropdown">
            <button
              class="btn btn-sm btn-label-secondary rounded-circle btn-icon border-0 position-relative"
              data-bs-toggle="dropdown"
            >
              <i class="far fa-bell"></i>
              <span
                class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger fs-9"
                >3</span
              >
            </button>
            <div
              class="dropdown-menu dropdown-menu-end shadow-lg border-0 rounded-3 p-0 dropdown-menu-320"
            >
              <div class="p-3 border-bottom d-flex align-items-center justify-content-between">
                <h6 class="m-0 fw-bold">Notifications</h6>
                <span class="badge bg-label-primary">3 New</span>
              </div>
              <div class="list-group list-group-flush fs-7">
                <a href="#" class="list-group-item list-group-item-action p-3 border-bottom">
                  <div class="d-flex gap-2">
                    <div class="badge bg-label-success rounded-circle p-2">
                      <i class="fas fa-shopping-bag"></i>
                    </div>
                    <div>
                      <div class="fw-semibold">New Order Received</div>
                      <div class="text-muted fs-8">Order #1092 placed 5 min ago</div>
                    </div>
                  </div>
                </a>
                <a href="#" class="list-group-item list-group-item-action p-3 border-bottom">
                  <div class="d-flex gap-2">
                    <div class="badge bg-label-warning rounded-circle p-2">
                      <i class="fas fa-user-plus"></i>
                    </div>
                    <div>
                      <div class="fw-semibold">New Customer Signup</div>
                      <div class="text-muted fs-8">User John Doe registered</div>
                    </div>
                  </div>
                </a>
              </div>
              <div class="p-2 text-center border-top">
                <small class="text-primary cursor-pointer fw-semibold"
                  >View All Notifications</small
                >
              </div>
            </div>
          </div>

          <!-- Profile Dropdown -->
          <div class="dropdown ms-1">
            <button
              class="btn p-0 border-0 d-flex align-items-center gap-2"
              data-bs-toggle="dropdown"
            >
              <img
                :src="profileImage"
                class="rounded-circle border"
                width="38"
                height="38"
                alt="Avatar"
              />
            </button>
            <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 rounded-3 p-2 mt-2">
              <li class="px-3 py-2 border-bottom mb-1">
                <div class="fw-bold">{{ user?.name || 'Administrator' }}</div>
                <div class="fs-8 text-muted">{{ user?.email || 'admin@demo.com' }}</div>
              </li>
              <li>
                <router-link to="/profile" class="dropdown-item rounded-2 py-2">
                  <i class="fas fa-user me-2 text-primary"></i>My Profile
                </router-link>
              </li>
              <li>
                <router-link to="/profile" class="dropdown-item rounded-2 py-2">
                  <i class="fas fa-sliders me-2 text-info"></i>Settings
                </router-link>
              </li>
              <li><hr class="dropdown-divider" /></li>
              <li>
                <button class="dropdown-item rounded-2 py-2 text-danger" @click="handleLogout">
                  <i class="fas fa-power-off me-2"></i>Logout
                </button>
              </li>
            </ul>
          </div>
        </div>
      </header>

      <!-- Global Search Modal -->
      <div v-if="showSearchModal" class="modal fade show d-block modal-backdrop-dark" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
          <div class="modal-content shadow-lg border-0 rounded-4">
            <div class="modal-header border-0 p-3 pb-0">
              <div class="input-group">
                <span class="input-group-text bg-transparent border-0"
                  ><i class="fas fa-search text-muted fs-5"></i
                ></span>
                <input
                  type="text"
                  v-model="searchQuery"
                  class="form-control border-0 shadow-none fs-5"
                  placeholder="Search products, orders, categories..."
                  autofocus
                />
                <button
                  class="btn btn-sm btn-icon text-muted border-0"
                  @click="showSearchModal = false"
                >
                  <i class="fas fa-times"></i>
                </button>
              </div>
            </div>
            <div class="modal-body p-3">
              <div class="text-muted fs-8 mb-2">QUICK LINKS</div>
              <div class="d-flex flex-column gap-1">
                <router-link
                  to="/products"
                  class="btn btn-light text-start border-0 rounded-3 py-2 px-3 d-flex justify-content-between align-items-center"
                  @click="showSearchModal = false"
                >
                  <span><i class="fas fa-box me-2 text-primary"></i> Products Directory</span>
                  <i class="fas fa-arrow-right fs-8 text-muted"></i>
                </router-link>
                <router-link
                  to="/orders"
                  class="btn btn-light text-start border-0 rounded-3 py-2 px-3 d-flex justify-content-between align-items-center"
                  @click="showSearchModal = false"
                >
                  <span
                    ><i class="fas fa-shopping-cart me-2 text-success"></i> Recent Sales
                    Orders</span
                  >
                  <i class="fas fa-arrow-right fs-8 text-muted"></i>
                </router-link>
                <router-link
                  to="/users"
                  class="btn btn-light text-start border-0 rounded-3 py-2 px-3 d-flex justify-content-between align-items-center"
                  @click="showSearchModal = false"
                >
                  <span><i class="fas fa-users me-2 text-warning"></i> Customer Accounts</span>
                  <i class="fas fa-arrow-right fs-8 text-muted"></i>
                </router-link>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Page Body View -->
      <main class="vx-admin-body">
        <slot></slot>
      </main>
    </div>
  </div>
</template>

<script>
import { mapState, mapActions } from 'pinia'
import { useAuthStore } from '../../stores/auth'

export default {
  name: 'AdminLayout',
  data() {
    return {
      isCollapsed: false,
      isMobileOpen: false,
      openSubmenu: 'catalog',
      isDarkMode: false,
      isFullscreen: false,
      showSearchModal: false,
      searchQuery: '',
    }
  },
  computed: {
    ...mapState(useAuthStore, ['user', 'isAuthenticated', 'isAdmin']),
    profileImage() {
      return `https://ui-avatars.com/api/?name=${encodeURIComponent(this.user?.name || 'Admin')}&background=7367f0&color=fff&size=150`
    },
    currentPageTitle() {
      const name = this.$route.name || ''
      if (name.includes('dashboard')) return 'Dashboard'
      if (name.includes('product')) return 'Products'
      if (name.includes('category')) return 'Categories'
      if (name.includes('order')) return 'Orders'
      if (name.includes('coupon')) return 'Coupons'
      if (name.includes('user')) return 'Users'
      if (name.includes('color')) return 'Colors'
      if (name.includes('size')) return 'Sizes'
      if (name.includes('profile')) return 'Settings'
      return 'Admin Panel'
    },
    isCatalogActive() {
      return [
        'products',
        'categories',
        'colors',
        'sizes',
        'product-create',
        'product-edit',
        'category-create',
        'category-edit',
      ].includes(this.$route.name)
    },
    isSalesActive() {
      return ['orders', 'coupons', 'order-detail', 'coupon-create', 'coupon-edit'].includes(
        this.$route.name
      )
    },
  },
  mounted() {
    const savedTheme = localStorage.getItem('theme') || 'light'
    this.isDarkMode = savedTheme === 'dark'
    document.documentElement.setAttribute('data-theme', savedTheme)

    // Keyboard shortcut for search modal
    window.addEventListener('keydown', this.handleKeydown)
  },
  beforeUnmount() {
    window.removeEventListener('keydown', this.handleKeydown)
  },
  methods: {
    ...mapActions(useAuthStore, ['logout']),
    toggleSidebar() {
      this.isCollapsed = !this.isCollapsed
    },
    toggleSubmenu(menu) {
      if (this.isCollapsed) this.isCollapsed = false
      this.openSubmenu = this.openSubmenu === menu ? null : menu
    },
    toggleTheme() {
      this.isDarkMode = !this.isDarkMode
      const theme = this.isDarkMode ? 'dark' : 'light'
      document.documentElement.setAttribute('data-theme', theme)
      localStorage.setItem('theme', theme)
    },
    toggleFullscreen() {
      if (!document.fullscreenElement) {
        document.documentElement.requestFullscreen()
        this.isFullscreen = true
      } else {
        if (document.exitFullscreen) {
          document.exitFullscreen()
          this.isFullscreen = false
        }
      }
    },
    handleKeydown(e) {
      if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
        e.preventDefault()
        this.showSearchModal = true
      }
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
