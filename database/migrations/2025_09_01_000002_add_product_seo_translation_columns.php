<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if (! Schema::hasColumn('products', 'seo_title_translations')) {
                $table->json('seo_title_translations')->nullable()->after('seo_title');
            }
            if (! Schema::hasColumn('products', 'seo_description_translations')) {
                $table->json('seo_description_translations')->nullable()->after('seo_description');
            }
            if (! Schema::hasColumn('products', 'seo_keywords_translations')) {
                $table->json('seo_keywords_translations')->nullable()->after('seo_keywords');
            }
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if (Schema::hasColumn('products', 'seo_title_translations')) {
                $table->dropColumn('seo_title_translations');
            }
            if (Schema::hasColumn('products', 'seo_description_translations')) {
                $table->dropColumn('seo_description_translations');
            }
            if (Schema::hasColumn('products', 'seo_keywords_translations')) {
                $table->dropColumn('seo_keywords_translations');
            }
        });
    }
};
