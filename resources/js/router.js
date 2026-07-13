import { createRouter, createWebHistory } from 'vue-router'
import Main from './components/Main.vue'

const routes = [
  {
    path: '/',
    component: Main,
    children: [
      {
        path: 'login',
        component: () => import('./components/Login.vue'),
        name: 'login',
        meta: { requiresGuest: true }
      },
      {
        path: 'register',
        component: () => import('./components/Register.vue'),
        name: 'register',
        meta: { requiresGuest: true }
      },
      {
        path: 'dashboard',
        component: () => import('./components/Dashboard.vue'),
        name: 'dashboard',
        meta: { requiresAuth: true, requiresAdmin: true }
      },
      {
        path: 'users',
        component: () => import('./components/Users.vue'),
        name: 'users',
        meta: { requiresAuth: true, requiresAdmin: true }
      },
      {
        path: 'users/create',
        component: () => import('./components/UserForm.vue'),
        name: 'user-create',
        meta: { requiresAuth: true, requiresAdmin: true }
      },
      {
        path: 'users/edit/:id',
        component: () => import('./components/UserForm.vue'),
        name: 'user-edit',
        meta: { requiresAuth: true, requiresAdmin: true }
      },
      {
        path: 'users/detail/:id',
        component: () => import('./components/UserDetail.vue'),
        name: 'user-detail',
        meta: { requiresAuth: true, requiresAdmin: true }
      },
      {
        path: 'products',
        component: () => import('./components/Products.vue'),
        name: 'products',
        meta: { requiresAuth: true, requiresAdmin: true }
      },
      {
        path: 'products/create',
        component: () => import('./components/ProductForm.vue'),
        name: 'product-create',
        meta: { requiresAuth: true, requiresAdmin: true }
      },
      {
        path: 'products/edit/:id',
        component: () => import('./components/ProductForm.vue'),
        name: 'product-edit',
        meta: { requiresAuth: true, requiresAdmin: true }
      },
      {
        path: 'categories',
        component: () => import('./components/Categories.vue'),
        name: 'categories',
        meta: { requiresAuth: true, requiresAdmin: true }
      },
      {
        path: 'categories/create',
        component: () => import('./components/CategoryForm.vue'),
        name: 'category-create',
        meta: { requiresAuth: true, requiresAdmin: true }
      },
      {
        path: 'categories/edit/:id',
        component: () => import('./components/CategoryForm.vue'),
        name: 'category-edit',
        meta: { requiresAuth: true, requiresAdmin: true }
      },
      {
        path: 'product',
        component: () => import('./components/Product.vue'),
        name: 'product',
        meta: { requiresAuth: true }
      },
      {
        path: 'my-cart',
        component: () => import('./components/MyCart.vue'),
        name: 'my-cart',
        meta: { requiresAuth: true }
      },
      {
        path: 'my-wishlist',
        component: () => import('./components/MyWishlist.vue'),
        name: 'my-wishlist',
        meta: { requiresAuth: true }
      },
      {
        path: 'checkout',
        component: () => import('./components/Checkout.vue'),
        name: 'checkout',
        meta: { requiresAuth: true }
      },
      {
        path: 'profile',
        component: () => import('./components/Profile.vue'),
        name: 'profile',
        meta: { requiresAuth: true }
      },
      {
        path: 'my-orders',
        component: () => import('./components/MyOrders.vue'),
        name: 'my-orders',
        meta: { requiresAuth: true }
      },
      {
        path: 'orders',
        component: () => import('./components/Orders.vue'),
        name: 'orders',
        meta: { requiresAuth: true , requiresAdmin: true }
      },
      {
        path: 'orders/detail/:id',
        component: () => import('./components/OrderDetail.vue'),
        name: 'order-detail',
        meta: { requiresAuth: true, requiresAdmin: true }
      },
      {
        path: 'product/detail/:id',
        component: () => import('./components/ProductDetail.vue'),
        name: 'product-detail',
        meta: { requiresAuth: true }
      },
    ]
  }
];

const router = createRouter({
    history: createWebHistory(import.meta.env.BASE_URL),
    routes,
})

// Route guards
router.beforeEach(async (to, from, next) => {
  try {
    const axios = (await import('axios')).default;

    const response = await axios.get('/user');
    const user = response.data;
    
    if (to.matched.some(record => record.meta.requiresGuest)) {
      next({ path: '/' });
      return;
    }
    
    if (to.matched.some(record => record.meta.requiresAuth)) {
      if (to.matched.some(record => record.meta.requiresAdmin)) {
        if (user.role !== 'admin') {
          next({ path: '/' });
          return;
        }
      }
      
      next();
      return;
    }
    
    next();
    
  } catch (error) {
    if (to.matched.some(record => record.meta.requiresGuest)) {
      next();
      return;
    }
    
    if (to.matched.some(record => record.meta.requiresAuth)) {
      next({ name: 'login', query: { redirect: to.fullPath } });
      return;
    }
    
    next();
  }
})

export default router