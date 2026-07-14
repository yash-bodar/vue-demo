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
        Schema::table('orders', function (Blueprint $table) {
            $table->foreignId('coupon_id')->nullable()->constrained('coupons')->onDelete('set null');
            $table->decimal('discount_amount', 10, 2)->default(0); // Amount discounted
            $table->decimal('final_amount', 10, 2); // Amount after discount
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeignKeyIfExists(['coupon_id']);
            $table->dropColumn(['coupon_id', 'discount_amount', 'final_amount']);
        });
    }
};
