<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if (! Schema::hasColumn('products', 'name_translations')) {
                $table->json('name_translations')->nullable()->after('name');
            }
            if (! Schema::hasColumn('products', 'slug_translations')) {
                $table->json('slug_translations')->nullable()->after('slug');
            }
            if (! Schema::hasColumn('products', 'short_description_translations')) {
                $table->json('short_description_translations')->nullable()->after('short_description');
            }
            if (! Schema::hasColumn('products', 'description_translations')) {
                $table->json('description_translations')->nullable()->after('description');
            }
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if (Schema::hasColumn('products', 'name_translations')) {
                $table->dropColumn('name_translations');
            }
            if (Schema::hasColumn('products', 'slug_translations')) {
                $table->dropColumn('slug_translations');
            }
            if (Schema::hasColumn('products', 'short_description_translations')) {
                $table->dropColumn('short_description_translations');
            }
            if (Schema::hasColumn('products', 'description_translations')) {
                $table->dropColumn('description_translations');
            }
        });
    }
};
