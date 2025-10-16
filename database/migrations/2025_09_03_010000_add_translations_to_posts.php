<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            if (! Schema::hasColumn('posts', 'title_translations')) {
                $table->json('title_translations')->nullable();
            }
            if (! Schema::hasColumn('posts', 'slug_translations')) {
                $table->json('slug_translations')->nullable();
            }
            if (! Schema::hasColumn('posts', 'excerpt_translations')) {
                $table->json('excerpt_translations')->nullable();
            }
            if (! Schema::hasColumn('posts', 'body_translations')) {
                $table->json('body_translations')->nullable();
            }
            if (! Schema::hasColumn('posts', 'seo_title_translations')) {
                $table->json('seo_title_translations')->nullable();
            }
            if (! Schema::hasColumn('posts', 'seo_description_translations')) {
                $table->json('seo_description_translations')->nullable();
            }
            if (! Schema::hasColumn('posts', 'seo_tags_translations')) {
                $table->json('seo_tags_translations')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $drops = [
                'title_translations', 'slug_translations', 'excerpt_translations', 'body_translations',
                'seo_title_translations', 'seo_description_translations', 'seo_tags_translations',
            ];
            foreach ($drops as $c) {
                if (Schema::hasColumn('posts', $c)) {
                    $table->dropColumn($c);
                }
            }
        });
    }
};
