<template>
  <div class="container py-4">
    <!-- Hero Banner Section -->
    <div class="row align-items-center mb-5 p-4 py-5 rounded-4 shadow-lg text-white" style="background: linear-gradient(135deg, #1e1b4b 0%, #312e81 50%, #4338ca 100%); position: relative; overflow: hidden;">
      <!-- Background subtle shapes -->
      <div class="position-absolute translate-middle rounded-circle bg-info opacity-10" style="width: 300px; height: 300px; top: 10%; left: 80%; filter: blur(60px);"></div>
      <div class="position-absolute translate-middle rounded-circle bg-primary opacity-10" style="width: 250px; height: 250px; bottom: -10%; left: 10%; filter: blur(50px);"></div>
      
      <div class="col-lg-7 px-4 position-relative">
        <span class="badge bg-primary-soft text-white px-3 py-2 rounded-pill fw-semibold mb-3">
          <i class="fas fa-sparkles me-2 text-warning"></i>Summer Collection 2026
        </span>
        <h1 class="display-4 fw-extrabold mb-3 leading-tight text-white">
          Discover a Premium Shopping Experience
        </h1>
        <p class="fs-5 text-light opacity-80 mb-4">
          Upgrade your style with our curated collections. Handpicked quality products with secure transactions and worldwide delivery.
        </p>
        <div class="d-flex flex-wrap gap-3">
          <router-link to="/product" class="btn btn-light btn-lg px-4 py-3 fw-bold text-dark shadow-sm">
            <i class="fas fa-shopping-bag me-2"></i>Explore Products
          </router-link>
          <router-link to="/my-cart" class="btn btn-outline-light btn-lg px-4 py-3 fw-bold">
            <i class="fas fa-shopping-cart me-2"></i>View Cart
          </router-link>
        </div>
      </div>
      
      <div class="col-lg-5 text-center d-none d-lg-block position-relative">
        <!-- Floating graphic item representation -->
        <div class="hero-image-wrapper p-4 bg-white bg-opacity-5 rounded-4 border border-white border-opacity-10 shadow-lg" style="backdrop-filter: blur(10px); animation: float 6s ease-in-out infinite;">
          <i class="fas fa-store-alt fa-10x text-white opacity-85"></i>
          <div class="mt-3 text-center">
            <span class="text-white-50 small">Premium Partners & Curated Inventory</span>
          </div>
        </div>
      </div>
    </div>

    <!-- Value Propositions Section -->
    <div class="row g-4 mb-5">
      <div class="col-md-6 col-lg-3">
        <div class="card h-100 p-4 border-0 text-center shadow-sm hover-grow">
          <div class="d-inline-flex align-items-center justify-content-center bg-primary-soft text-primary rounded-circle mb-3 mx-auto" style="width: 60px; height: 60px;">
            <i class="fas fa-truck fs-4"></i>
          </div>
          <h5 class="fw-bold mb-2">Free Shipping</h5>
          <p class="text-muted small mb-0">Free priority shipping on orders over $50 USD to all supported regions.</p>
        </div>
      </div>
      
      <div class="col-md-6 col-lg-3">
        <div class="card h-100 p-4 border-0 text-center shadow-sm hover-grow">
          <div class="d-inline-flex align-items-center justify-content-center bg-primary-soft text-primary rounded-circle mb-3 mx-auto" style="width: 60px; height: 60px;">
            <i class="fas fa-shield-alt fs-4"></i>
          </div>
          <h5 class="fw-bold mb-2">Secure Payments</h5>
          <p class="text-muted small mb-0">SSL-encrypted payments verified directly via Stripe gateway integration.</p>
        </div>
      </div>
      
      <div class="col-md-6 col-lg-3">
        <div class="card h-100 p-4 border-0 text-center shadow-sm hover-grow">
          <div class="d-inline-flex align-items-center justify-content-center bg-primary-soft text-primary rounded-circle mb-3 mx-auto" style="width: 60px; height: 60px;">
            <i class="fas fa-undo-alt fs-4"></i>
          </div>
          <h5 class="fw-bold mb-2">Easy Returns</h5>
          <p class="text-muted small mb-0">Hassle-free 30-day money-back guarantee on all eligible purchases.</p>
        </div>
      </div>
      
      <div class="col-md-6 col-lg-3">
        <div class="card h-100 p-4 border-0 text-center shadow-sm hover-grow">
          <div class="d-inline-flex align-items-center justify-content-center bg-primary-soft text-primary rounded-circle mb-3 mx-auto" style="width: 60px; height: 60px;">
            <i class="fas fa-headset fs-4"></i>
          </div>
          <h5 class="fw-bold mb-2">24/7 Support</h5>
          <p class="text-muted small mb-0">Get priority customer assistance from our experts at any time.</p>
        </div>
      </div>
    </div>

    <!-- Quick Navigation / Promos Section -->
    <div class="row align-items-center g-4 mb-4">
      <div class="col-lg-6">
        <div class="p-4 rounded-4 shadow-sm text-white" style="background: linear-gradient(135deg, #4f46e5 0%, #06b6d4 100%);">
          <h4 class="fw-bold mb-2">Special Offers</h4>
          <p class="mb-3 opacity-90">Apply unique promotional coupon codes at checkout to grab up to 50% discounts.</p>
          <router-link to="/product" class="btn btn-light btn-sm fw-bold px-3 py-2 text-primary shadow-sm">
            Shop Deals
          </router-link>
        </div>
      </div>
      <div class="col-lg-6">
        <div class="p-4 rounded-4 shadow-sm text-white" style="background: linear-gradient(135deg, #ec4899 0%, #f43f5e 100%);">
          <h4 class="fw-bold mb-2">Wishlist & Save</h4>
          <p class="mb-3 opacity-90">Keep track of your favorite goods. Save them to your personal wishlist and buy later.</p>
          <router-link to="/my-wishlist" class="btn btn-light btn-sm fw-bold px-3 py-2 text-danger shadow-sm">
            My Wishlist
          </router-link>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'Home'
}
</script>

<style scoped>
.hover-grow {
  transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1), box-shadow 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}
.hover-grow:hover {
  transform: translateY(-4px);
  box-shadow: 0 12px 24px -10px rgba(0, 0, 0, 0.1) !important;
}
.hero-image-wrapper {
  display: inline-block;
}
@keyframes float {
  0% { transform: translateY(0px) rotate(0deg); }
  50% { transform: translateY(-10px) rotate(2deg); }
  100% { transform: translateY(0px) rotate(0deg); }
}
</style>
