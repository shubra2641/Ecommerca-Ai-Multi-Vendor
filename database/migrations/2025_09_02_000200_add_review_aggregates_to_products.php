<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if (! Schema::hasColumn('products', 'approved_reviews_count')) {
                $table->unsignedInteger('approved_reviews_count')->default(0)->after('is_best_seller');
            }
            if (! Schema::hasColumn('products', 'approved_reviews_avg')) {
                $table->decimal('approved_reviews_avg', 3, 2)->default(0)->after('approved_reviews_count');
            }
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if (Schema::hasColumn('products', 'approved_reviews_avg')) {
                $table->dropColumn('approved_reviews_avg');
            }
            if (Schema::hasColumn('products', 'approved_reviews_count')) {
                $table->dropColumn('approved_reviews_count');
            }
        });
    }
};
