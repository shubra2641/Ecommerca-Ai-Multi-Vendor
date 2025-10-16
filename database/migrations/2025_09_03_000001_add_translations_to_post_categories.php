<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('post_categories')) {
            return;
        } // safety
        Schema::table('post_categories', function (Blueprint $table) {
            // Only add columns if they don't already exist (idempotent)
            $cols = Schema::getColumnListing('post_categories');
            $add = function (string $name, callable $definition) use ($cols, $table) {
                if (! in_array($name, $cols)) {
                    $definition($table);
                }
            };
            $add('name_translations', fn ($t) => $t->json('name_translations')->nullable());
            $add('slug_translations', fn ($t) => $t->json('slug_translations')->nullable());
            $add('description_translations', fn ($t) => $t->json('description_translations')->nullable());
            $add('seo_title_translations', fn ($t) => $t->json('seo_title_translations')->nullable());
            $add('seo_description_translations', fn ($t) => $t->json('seo_description_translations')->nullable());
            $add('seo_tags_translations', fn ($t) => $t->json('seo_tags_translations')->nullable());
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('post_categories')) {
            return;
        }
        Schema::table('post_categories', function (Blueprint $table) {
            foreach (
                [
                    'name_translations', 'slug_translations', 'description_translations',
                    'seo_title_translations', 'seo_description_translations', 'seo_tags_translations',
                ] as $col
            ) {
                if (Schema::hasColumn('post_categories', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
