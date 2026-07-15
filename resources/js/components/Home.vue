<template>
  <div class="home-container">
    <div class="text-center py-5">
      <div class="mb-4">
        <i class="fas fa-store fa-5x text-primary"></i>
      </div>
      <h1 class="text-primary fw-bold mb-3">Welcome to VueShop</h1>
      <p class="text-muted mb-4">Browse our amazing collection of products</p>
      <router-link to="/product" class="btn btn-primary btn-lg bg-primary-linear">
        <i class="fas fa-shopping-bag me-2"></i>Start Shopping
      </router-link>
    </div>
  </div>
</template>

<script>
export default {
  name: 'Home'
}
</script>

