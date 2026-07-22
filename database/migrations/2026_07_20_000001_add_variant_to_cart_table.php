<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cart', function (Blueprint $table) {
            // Drop foreign keys first to allow dropping the index
            $table->dropForeign(['user_id']);
            $table->dropForeign(['product_id']);
            
            // Now drop the old unique index
            $table->dropUnique(['user_id', 'product_id']);
            
            // Add variant column
            $table->foreignId('product_variant_id')
                ->nullable()
                ->after('product_id')
                ->constrained('product_variants')
                ->cascadeOnDelete();

            // Re-add user_id and product_id foreign keys
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            $table->foreign('product_id')->references('id')->on('products')->cascadeOnDelete();

            // Add new unique index including the variant
            $table->unique(['user_id', 'product_id', 'product_variant_id']);
        });
    }

    public function down(): void
    {
        Schema::table('cart', function (Blueprint $table) {
            // Drop unique index
            $table->dropUnique(['user_id', 'product_id', 'product_variant_id']);
            
            // Drop foreign keys
            $table->dropForeign(['user_id']);
            $table->dropForeign(['product_id']);
            $table->dropForeign(['product_variant_id']);
            
            // Drop column
            $table->dropColumn('product_variant_id');

            // Re-add original foreign keys and unique constraint
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            $table->foreign('product_id')->references('id')->on('products')->cascadeOnDelete();
            $table->unique(['user_id', 'product_id']);
        });
    }
};
