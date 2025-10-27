<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('products')) {
            Schema::table('products', function (Blueprint $table) {
                // Rely on try/catch to avoid platform-specific introspection
                try {
                    $table->index(['product_category_id', 'active'], 'products_category_active_idx');
                } catch (\Throwable $e) {
                }
            });
        }
        if (Schema::hasTable('product_reviews')) {
            Schema::table('product_reviews', function (Blueprint $table) {
                try {
                    $table->index(['product_id', 'approved'], 'product_reviews_product_approved_idx');
                } catch (\Throwable $e) {
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('products')) {
            Schema::table('products', function (Blueprint $table) {
                try {
                    $table->dropIndex('products_category_active_idx');
                } catch (\Throwable $e) {
                }
            });
        }
        if (Schema::hasTable('product_reviews')) {
            Schema::table('product_reviews', function (Blueprint $table) {
                try {
                    $table->dropIndex('product_reviews_product_approved_idx');
                } catch (\Throwable $e) {
                }
            });
        }
    }

    private function hasIndex(string $table, string $indexName): bool
    {
        return false;
    }
};
