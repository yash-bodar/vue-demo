<template>
  <div class="container py-5">
    <!-- Hero Banner Section -->
    <div class="row align-items-center mb-5 p-5 rounded-4 text-white position-relative overflow-hidden" 
         style="background: linear-gradient(135deg, #1e1b4b 0%, #312e81 40%, #4f46e5 100%); box-shadow: 0 20px 40px -15px rgba(79, 70, 229, 0.4);">
      
      <!-- Decorative background blur blobs -->
      <div class="position-absolute rounded-circle bg-info opacity-20" style="width: 300px; height: 300px; top: -10%; right: -5%; filter: blur(70px); pointer-events: none;"></div>
      <div class="position-absolute rounded-circle bg-primary opacity-20" style="width: 250px; height: 250px; bottom: -10%; left: -5%; filter: blur(60px); pointer-events: none;"></div>
      <div class="position-absolute rounded-circle bg-pink opacity-10" style="width: 200px; height: 200px; top: 40%; left: 40%; filter: blur(50px); pointer-events: none;"></div>

      <div class="col-lg-7 px-4 position-relative" style="z-index: 2;">
        <span class="badge badge-primary-soft text-white px-3 py-2 rounded-pill fw-semibold mb-3" style="background: rgba(255, 255, 255, 0.1) !important; border: 1px solid rgba(255, 255, 255, 0.2);">
          <i class="fas fa-sparkles me-2 text-warning"></i>Summer Collection 2026
        </span>
        <h1 class="display-4 fw-extrabold mb-3 leading-tight text-white" style="letter-spacing: -0.03em;">
          Discover a Premium <br>Shopping Experience
        </h1>
        <p class="fs-5 text-light opacity-80 mb-4" style="max-width: 550px; line-height: 1.6;">
          Upgrade your style with our curated collections. Handpicked quality products with secure transactions and worldwide delivery.
        </p>
        <div class="d-flex flex-wrap gap-3">
          <router-link to="/product" class="btn btn-light btn-lg px-4 py-3 fw-bold text-primary shadow-sm hover-scale">
            <i class="fas fa-shopping-bag me-2"></i>Explore Products
          </router-link>
          <router-link to="/my-cart" class="btn btn-outline-light btn-lg px-4 py-3 fw-bold hover-scale">
            <i class="fas fa-shopping-cart me-2"></i>View Cart
          </router-link>
        </div>
      </div>
      
      <div class="col-lg-5 text-center d-none d-lg-block position-relative" style="z-index: 2;">
        <div class="hero-image-wrapper p-5 bg-white bg-opacity-5 rounded-4 border border-white border-opacity-10 shadow-lg" 
             style="backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px); animation: float 6s ease-in-out infinite;">
          <i class="fas fa-shopping-basket fa-10x text-white opacity-90"></i>
          <div class="mt-4 text-center">
            <span class="text-white-50 fw-semibold">Premium Products & Verified Inventory</span>
          </div>
        </div>
      </div>
    </div>

    <!-- Value Propositions Section -->
    <div class="row g-4 mb-5">
      <div class="col-md-6 col-lg-3">
        <div class="card card-premium h-100 p-4 text-center">
          <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-3 mx-auto" 
               style="width: 60px; height: 60px; background-color: rgba(99, 102, 241, 0.08); color: #6366f1;">
            <i class="fas fa-truck fs-4"></i>
          </div>
          <h5 class="fw-bold mb-2">Free Shipping</h5>
          <p class="text-muted small mb-0">Free priority shipping on orders over $50 USD to all supported regions.</p>
        </div>
      </div>
      
      <div class="col-md-6 col-lg-3">
        <div class="card card-premium h-100 p-4 text-center">
          <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-3 mx-auto" 
               style="width: 60px; height: 60px; background-color: rgba(14, 165, 233, 0.08); color: #0ea5e9;">
            <i class="fas fa-shield-alt fs-4"></i>
          </div>
          <h5 class="fw-bold mb-2">Secure Payments</h5>
          <p class="text-muted small mb-0">SSL-encrypted payments verified directly via Stripe gateway integration.</p>
        </div>
      </div>
      
      <div class="col-md-6 col-lg-3">
        <div class="card card-premium h-100 p-4 text-center">
          <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-3 mx-auto" 
               style="width: 60px; height: 60px; background-color: rgba(16, 185, 129, 0.08); color: #10b981;">
            <i class="fas fa-undo-alt fs-4"></i>
          </div>
          <h5 class="fw-bold mb-2">Easy Returns</h5>
          <p class="text-muted small mb-0">Hassle-free 30-day money-back guarantee on all eligible purchases.</p>
        </div>
      </div>
      
      <div class="col-md-6 col-lg-3">
        <div class="card card-premium h-100 p-4 text-center">
          <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-3 mx-auto" 
               style="width: 60px; height: 60px; background-color: rgba(245, 158, 11, 0.08); color: #f59e0b;">
            <i class="fas fa-headset fs-4"></i>
          </div>
          <h5 class="fw-bold mb-2">24/7 Support</h5>
          <p class="text-muted small mb-0">Get priority customer assistance from our experts at any time.</p>
        </div>
      </div>
    </div>

    <!-- Quick Navigation / Promos Section -->
    <div class="row align-items-center g-4">
      <div class="col-lg-6">
        <div class="p-5 rounded-4 text-white position-relative overflow-hidden" 
             style="background: linear-gradient(135deg, #4f46e5 0%, #06b6d4 100%); box-shadow: 0 10px 25px -5px rgba(79, 70, 229, 0.2);">
          <div class="position-relative" style="z-index: 2;">
            <h4 class="fw-bold mb-2">Special Offers</h4>
            <p class="mb-4 opacity-90" style="max-width: 450px;">Apply unique promotional coupon codes at checkout to grab up to 50% discounts.</p>
            <router-link to="/product" class="btn btn-light fw-bold px-4 py-2 text-primary shadow-sm hover-scale">
              Shop Deals
            </router-link>
          </div>
        </div>
      </div>
      <div class="col-lg-6">
        <div class="p-5 rounded-4 text-white position-relative overflow-hidden" 
             style="background: linear-gradient(135deg, #ec4899 0%, #f43f5e 100%); box-shadow: 0 10px 25px -5px rgba(236, 72, 153, 0.2);">
          <div class="position-relative" style="z-index: 2;">
            <h4 class="fw-bold mb-2">Wishlist & Save</h4>
            <p class="mb-4 opacity-90" style="max-width: 450px;">Keep track of your favorite goods. Save them to your personal wishlist and buy later.</p>
            <router-link to="/my-wishlist" class="btn btn-light fw-bold px-4 py-2 text-danger shadow-sm hover-scale">
              My Wishlist
            </router-link>
          </div>
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
.hover-scale {
  transition: transform 0.2s ease, box-shadow 0.2s ease;
}
.hover-scale:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 16px rgba(0,0,0,0.1) !important;
}
@keyframes float {
  0% { transform: translateY(0px) rotate(0deg); }
  50% { transform: translateY(-10px) rotate(1deg); }
  100% { transform: translateY(0px) rotate(0deg); }
}
</style>
