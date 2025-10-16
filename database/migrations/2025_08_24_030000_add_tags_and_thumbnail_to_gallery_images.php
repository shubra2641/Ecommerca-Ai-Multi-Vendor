<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('gallery_images', function (Blueprint $table) {
            if (! Schema::hasColumn('gallery_images', 'tags')) {
                $table->string('tags')->nullable()->after('alt');
            }
            if (! Schema::hasColumn('gallery_images', 'thumbnail_path')) {
                $table->string('thumbnail_path')->nullable()->after('webp_path');
            }
        });
    }

    public function down(): void
    {
        Schema::table('gallery_images', function (Blueprint $table) {
            if (Schema::hasColumn('gallery_images', 'tags')) {
                $table->dropColumn('tags');
            }
            if (Schema::hasColumn('gallery_images', 'thumbnail_path')) {
                $table->dropColumn('thumbnail_path');
            }
        });
    }
};
