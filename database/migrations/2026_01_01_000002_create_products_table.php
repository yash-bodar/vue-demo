<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description');
            $table->integer('stock');
            $table->decimal('price', 10, 2);
            $table->string('status')->default('Active');
            $table->string('currency')->default('USD');
            $table->string('image')->nullable();
            $table->foreignId('category_id')
                ->nullable()
                ->constrained('category')
                ->nullOnDelete()
                ->restrictOnUpdate();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
