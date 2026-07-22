<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('address_id')->constrained('addresses')->cascadeOnDelete();
            $table->string('currency')->default('USD');
            $table->decimal('total_amount', 10, 2);
            $table->enum('payment_status', ['cancelled', 'pending', 'paid'])->default('pending');
            $table->string('payment_intent_id')->nullable();
            $table->enum('status', [
                'pending', 'processing', 'shipped', 'delivered',
                'completed', 'refunded', 'cancelled',
            ])->default('pending');
            $table->json('items');
            $table->foreignId('coupon_id')
                ->nullable()
                ->constrained('coupons')
                ->nullOnDelete();
            $table->decimal('discount_amount', 10, 2)->default(0);
            $table->decimal('final_amount', 10, 2);
            $table->decimal('shipping', 10, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
