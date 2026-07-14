<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique(); // Coupon code like "SAVE20"
            $table->text('description')->nullable(); // e.g., "20% off on all products"
            $table->enum('discount_type', ['percentage', 'fixed']); // Either percentage (20%) or fixed amount ($10)
            $table->decimal('discount_value', 10, 2); // Discount value (20 for 20% or 10 for $10)
            $table->decimal('min_purchase_amount', 10, 2)->nullable(); // Minimum purchase required
            $table->integer('max_uses')->nullable(); // Total times coupon can be used (null = unlimited)
            $table->integer('times_used')->default(0); // Times already used
            $table->integer('max_uses_per_user')->nullable(); // Times per user (null = unlimited)
            $table->dateTime('valid_from')->nullable();
            $table->dateTime('valid_until')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('coupons');
    }
};
