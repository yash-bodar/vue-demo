<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::middleware('auth:sanctum')->group(function () {
    Route::get('/users',[\App\Http\Controllers\Api\UserController::class,'index']);
    Route::post('/users',[\App\Http\Controllers\Api\UserController::class,'store']);
    Route::get('/users/{id}',[\App\Http\Controllers\Api\UserController::class,'edit']);
    Route::get('/fetch-user/{id}',[\App\Http\Controllers\Api\UserController::class,'show']);
    Route::delete('/users/{id}',[\App\Http\Controllers\Api\UserController::class,'destroy']);

    Route::get('/products',[\App\Http\Controllers\Api\ProductController::class,'index']);
    Route::post('/products',[\App\Http\Controllers\Api\ProductController::class,'store']);
    Route::get('/products/{id}',[\App\Http\Controllers\Api\ProductController::class,'edit']);
    Route::delete('/products/{id}',[\App\Http\Controllers\Api\ProductController::class,'destroy']);
    Route::get('/get-products',[\App\Http\Controllers\Api\ProductController::class,'getProducts']);
    Route::get('/product/{id}',[\App\Http\Controllers\Api\ProductController::class,'show']);
    Route::get('/export-products',[\App\Http\Controllers\Api\ProductController::class,'exportProducts']);
    
    Route::get('/categories',[\App\Http\Controllers\Api\CategoryController::class,'index']);
    Route::post('/categories',[\App\Http\Controllers\Api\CategoryController::class,'store']);
    Route::get('/categories/{id}',[\App\Http\Controllers\Api\CategoryController::class,'edit']);
    Route::delete('/categories/{id}',[\App\Http\Controllers\Api\CategoryController::class,'destroy']);
    Route::get('/get-categories',[\App\Http\Controllers\Api\CategoryController::class,'getCategories']);
    
    Route::get('/cart',[\App\Http\Controllers\Api\CartController::class,'getCart']);
    Route::post('/update-cart',[\App\Http\Controllers\Api\CartController::class,'updateCart']);
    Route::get('/convert-amount',[\App\Http\Controllers\Api\CartController::class,'convertAmount']);
    Route::post('/checkout',[\App\Http\Controllers\Api\CartController::class,'checkout']);
    
    Route::post('/update-wishlist',[\App\Http\Controllers\Api\WishlistController::class,'updateWishlist']);
    Route::get('/get-wishlist',[\App\Http\Controllers\Api\WishlistController::class,'getWishlist']);
    Route::get('/fetch-wishlist',[\App\Http\Controllers\Api\WishlistController::class,'fetchWishlist']);

    Route::get('/get-addresses',[\App\Http\Controllers\Api\ProfileController::class,'getAddresses']);
    Route::post('/update-address',[\App\Http\Controllers\Api\ProfileController::class,'updateAddress']);
    Route::put('/profile',[\App\Http\Controllers\Api\ProfileController::class,'update']);
    Route::put('/change-password',[\App\Http\Controllers\Api\ProfileController::class,'changePassword']);
    
    Route::get('/get-orders',[\App\Http\Controllers\Api\OrderController::class,'getOrders']);
    Route::get('/orders',[\App\Http\Controllers\Api\OrderController::class,'index']);
    Route::get('/order-detail/{id}',[\App\Http\Controllers\Api\OrderController::class,'getOrderDetail']);
    Route::get('/export-orders',[\App\Http\Controllers\Api\OrderController::class,'exportOrders']);
    Route::get('/download-invoice/{id}',[\App\Http\Controllers\Api\OrderController::class,'downloadInvoice']);
    Route::put('/orders/{id}/status',[\App\Http\Controllers\Api\OrderController::class,'updateStatus']);
    Route::put('/orders/{id}/payment-status',[\App\Http\Controllers\Api\OrderController::class,'updatePaymentStatus']);

    Route::get('/product-ratings/{productId}',[\App\Http\Controllers\Api\ProductRatingController::class,'index']);
    Route::post('/product-ratings',[\App\Http\Controllers\Api\ProductRatingController::class,'store']);
    Route::delete('/delete-product-rating/{ratingId}',[\App\Http\Controllers\Api\ProductRatingController::class,'deleteRating']);
    
    Route::get('/checkout-data',[\App\Http\Controllers\Api\CheckoutController::class,'getCheckoutData']);
    Route::post('/create-payment-intent',[\App\Http\Controllers\Api\CheckoutController::class,'createPaymentIntent']);
    Route::post('/place-order',[\App\Http\Controllers\Api\CheckoutController::class,'placeOrder']);

    Route::get('/get-dashboard-data',[\App\Http\Controllers\Api\DashboardController::class,'getDashboardData']);

    // Coupon Routes
    Route::get('/coupons', [\App\Http\Controllers\Api\CouponController::class, 'index']); // Admin
    Route::post('/coupons', [\App\Http\Controllers\Api\CouponController::class, 'store']); // Admin
    Route::get('/coupons/{id}', [\App\Http\Controllers\Api\CouponController::class, 'show']); // Admin
    Route::put('/coupons/{id}', [\App\Http\Controllers\Api\CouponController::class, 'update']); // Admin
    Route::delete('/coupons/{id}', [\App\Http\Controllers\Api\CouponController::class, 'destroy']); // Admin
    Route::post('/coupons/validate', [\App\Http\Controllers\Api\CouponController::class, 'validate']); // User
});
