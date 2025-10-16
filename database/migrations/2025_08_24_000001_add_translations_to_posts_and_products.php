<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('posts')) {
            Schema::table('posts', function (Blueprint $table) {
                if (! Schema::hasColumn('posts', 'title_translations')) {
                    $table->json('title_translations')->nullable()->after('title');
                }
                if (! Schema::hasColumn('posts', 'slug_translations')) {
                    $table->json('slug_translations')->nullable()->after('slug');
                }
                if (! Schema::hasColumn('posts', 'excerpt_translations')) {
                    $table->json('excerpt_translations')->nullable()->after('excerpt');
                }
                if (! Schema::hasColumn('posts', 'body_translations')) {
                    $table->json('body_translations')->nullable()->after('body');
                }
                if (! Schema::hasColumn('posts', 'seo_title_translations')) {
                    $table->json('seo_title_translations')->nullable()->after('seo_title');
                }
                if (! Schema::hasColumn('posts', 'seo_description_translations')) {
                    $table->json('seo_description_translations')->nullable()->after('seo_description');
                }
                if (! Schema::hasColumn('posts', 'seo_tags_translations')) {
                    $table->json('seo_tags_translations')->nullable()->after('seo_tags');
                }
            });
        }

        if (Schema::hasTable('products')) {
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
    }

    public function down(): void
    {
        if (Schema::hasTable('posts')) {
            Schema::table('posts', function (Blueprint $table) {
                if (Schema::hasColumn('posts', 'title_translations')) {
                    $table->dropColumn('title_translations');
                }
                if (Schema::hasColumn('posts', 'slug_translations')) {
                    $table->dropColumn('slug_translations');
                }
                if (Schema::hasColumn('posts', 'excerpt_translations')) {
                    $table->dropColumn('excerpt_translations');
                }
                if (Schema::hasColumn('posts', 'body_translations')) {
                    $table->dropColumn('body_translations');
                }
                if (Schema::hasColumn('posts', 'seo_title_translations')) {
                    $table->dropColumn('seo_title_translations');
                }
                if (Schema::hasColumn('posts', 'seo_description_translations')) {
                    $table->dropColumn('seo_description_translations');
                }
                if (Schema::hasColumn('posts', 'seo_tags_translations')) {
                    $table->dropColumn('seo_tags_translations');
                }
            });
        }
        if (Schema::hasTable('products')) {
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
    }
};
