import { createRouter, createWebHistory } from 'vue-router'
import Main from './components/Main.vue'
import { useAuthStore } from './stores/auth'

const routes = [
  {
    path: '/',
    component: Main,
    children: [
      {
        path: '',
        component: () => import('./components/Home.vue'),
        name: 'home',
      },
      {
        path: 'login',
        component: () => import('./components/Login.vue'),
        name: 'login',
        meta: { requiresGuest: true },
      },
      {
        path: 'register',
        component: () => import('./components/Register.vue'),
        name: 'register',
        meta: { requiresGuest: true },
      },
      {
        path: 'forgot-password',
        component: () => import('./components/ForgotPassword.vue'),
        name: 'forgot-password',
        meta: { requiresGuest: true },
      },
      {
        path: 'reset-password',
        component: () => import('./components/ResetPassword.vue'),
        name: 'reset-password',
        meta: { requiresGuest: true },
      },
      {
        path: 'dashboard',
        component: () => import('./components/Dashboard.vue'),
        name: 'dashboard',
        meta: { requiresAuth: true, requiresAdmin: true },
      },
      {
        path: 'users',
        component: () => import('./components/Users.vue'),
        name: 'users',
        meta: { requiresAuth: true, requiresAdmin: true },
      },
      {
        path: 'users/create',
        component: () => import('./components/UserForm.vue'),
        name: 'user-create',
        meta: { requiresAuth: true, requiresAdmin: true },
      },
      {
        path: 'users/edit/:id',
        component: () => import('./components/UserForm.vue'),
        name: 'user-edit',
        meta: { requiresAuth: true, requiresAdmin: true },
      },
      {
        path: 'users/detail/:id',
        component: () => import('./components/UserDetail.vue'),
        name: 'user-detail',
        meta: { requiresAuth: true, requiresAdmin: true },
      },
      {
        path: 'products',
        component: () => import('./components/Products.vue'),
        name: 'products',
        meta: { requiresAuth: true, requiresAdmin: true },
      },
      {
        path: 'products/create',
        component: () => import('./components/ProductForm.vue'),
        name: 'product-create',
        meta: { requiresAuth: true, requiresAdmin: true },
      },
      {
        path: 'products/edit/:id',
        component: () => import('./components/ProductForm.vue'),
        name: 'product-edit',
        meta: { requiresAuth: true, requiresAdmin: true },
      },
      {
        path: 'categories',
        component: () => import('./components/Categories.vue'),
        name: 'categories',
        meta: { requiresAuth: true, requiresAdmin: true },
      },
      {
        path: 'categories/create',
        component: () => import('./components/CategoryForm.vue'),
        name: 'category-create',
        meta: { requiresAuth: true, requiresAdmin: true },
      },
      {
        path: 'categories/edit/:id',
        component: () => import('./components/CategoryForm.vue'),
        name: 'category-edit',
        meta: { requiresAuth: true, requiresAdmin: true },
      },
      {
        path: 'sizes',
        component: () => import('./components/Sizes.vue'),
        name: 'sizes',
        meta: { requiresAuth: true, requiresAdmin: true },
      },
      {
        path: 'colors',
        component: () => import('./components/Colors.vue'),
        name: 'colors',
        meta: { requiresAuth: true, requiresAdmin: true },
      },
      {
        path: 'product',
        component: () => import('./components/Product.vue'),
        name: 'product',
        meta: { requiresAuth: true },
      },
      {
        path: 'my-cart',
        component: () => import('./components/MyCart.vue'),
        name: 'my-cart',
        meta: { requiresAuth: true },
      },
      {
        path: 'my-wishlist',
        component: () => import('./components/MyWishlist.vue'),
        name: 'my-wishlist',
        meta: { requiresAuth: true },
      },
      {
        path: 'checkout',
        component: () => import('./components/Checkout.vue'),
        name: 'checkout',
        meta: { requiresAuth: true },
      },
      {
        path: 'profile',
        component: () => import('./components/Profile.vue'),
        name: 'profile',
        meta: { requiresAuth: true },
      },
      {
        path: 'my-orders',
        component: () => import('./components/MyOrders.vue'),
        name: 'my-orders',
        meta: { requiresAuth: true },
      },
      {
        path: 'orders',
        component: () => import('./components/Orders.vue'),
        name: 'orders',
        meta: { requiresAuth: true, requiresAdmin: true },
      },
      {
        path: 'orders/detail/:id',
        component: () => import('./components/OrderDetail.vue'),
        name: 'order-detail',
        meta: { requiresAuth: true, requiresAdmin: true },
      },
      {
        path: 'product/detail/:id',
        component: () => import('./components/ProductDetail.vue'),
        name: 'product-detail',
        meta: { requiresAuth: true },
      },
      {
        path: 'coupons',
        component: () => import('./components/Coupons.vue'),
        name: 'coupons',
        meta: { requiresAuth: true, requiresAdmin: true },
      },
      {
        path: 'coupons/create',
        component: () => import('./components/CouponForm.vue'),
        name: 'coupon-create',
        meta: { requiresAuth: true, requiresAdmin: true },
      },
      {
        path: 'coupons/edit/:id',
        component: () => import('./components/CouponForm.vue'),
        name: 'coupon-edit',
        meta: { requiresAuth: true, requiresAdmin: true },
      },
    ],
  },
]

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes,
})

router.beforeEach(async (to) => {
  const authStore = useAuthStore()
  await authStore.fetchUser()

  // Redirect admin users to dashboard when accessing home
  if (to.name === 'home' && authStore.isAuthenticated && authStore.isAdmin) {
    return { name: 'dashboard' }
  }

  if (to.matched.some((record) => record.meta.requiresGuest)) {
    if (authStore.isAuthenticated) {
      return { path: '/' }
    }
    return true
  }

  if (to.matched.some((record) => record.meta.requiresAuth)) {
    if (!authStore.isAuthenticated) {
      return { name: 'login', query: { redirect: to.fullPath } }
    }
    if (to.matched.some((record) => record.meta.requiresAdmin) && !authStore.isAdmin) {
      return { path: '/' }
    }
    return true
  }

  return true
})

export default router
