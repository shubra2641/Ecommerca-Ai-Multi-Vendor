<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('product_categories', function (Blueprint $table) {
            if (! Schema::hasColumn('product_categories', 'commission_rate')) {
                $table->decimal('commission_rate', 5, 2)->nullable()->after('position');
            }
            if (! Schema::hasColumn('product_categories', 'name_translations')) {
                $table->json('name_translations')->nullable()->after('name');
            }
            if (! Schema::hasColumn('product_categories', 'description_translations')) {
                $table->json('description_translations')->nullable()->after('description');
            }
        });
    }

    public function down(): void
    {
        Schema::table('product_categories', function (Blueprint $table) {
            if (Schema::hasColumn('product_categories', 'commission_rate')) {
                $table->dropColumn('commission_rate');
            }
            if (Schema::hasColumn('product_categories', 'name_translations')) {
                $table->dropColumn('name_translations');
            }
            if (Schema::hasColumn('product_categories', 'description_translations')) {
                $table->dropColumn('description_translations');
            }
        });
    }
};
