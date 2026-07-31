<template>
  <component :is="activeLayout">
    <router-view v-slot="{ Component }">
      <transition name="fade" mode="out-in">
        <component :is="Component" />
      </transition>
    </router-view>
  </component>
</template>

<script>
import { mapState } from 'pinia'
import { useAuthStore } from '../stores/auth'
import AdminLayout from './admin/AdminLayout.vue'
import CustomerLayout from './customer/CustomerLayout.vue'

export default {
  name: 'Main',
  components: {
    AdminLayout,
    CustomerLayout,
  },
  computed: {
    ...mapState(useAuthStore, ['isAuthenticated', 'isAdmin']),
    activeLayout() {
      // If user is authenticated and is an admin, render AdminLayout
      if (this.isAuthenticated && this.isAdmin) {
        return 'AdminLayout'
      }
      // Otherwise render CustomerLayout
      return 'CustomerLayout'
    },
  },
}
</script>

<style>
.fade-enter-active,
.fade-leave-active {
  transition:
    opacity 0.2s ease,
    transform 0.2s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
  transform: translateY(4px);
}
</style>
