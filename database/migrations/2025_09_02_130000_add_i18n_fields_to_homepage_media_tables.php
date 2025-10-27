<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('homepage_slides', function (Blueprint $table) {
            $table->json('title_i18n')->nullable()->after('title');
            $table->json('subtitle_i18n')->nullable()->after('subtitle');
            $table->json('button_text_i18n')->nullable()->after('button_text');
        });
        Schema::table('homepage_banners', function (Blueprint $table) {
            $table->json('alt_text_i18n')->nullable()->after('alt_text');
        });
    }

    public function down(): void
    {
        Schema::table('homepage_slides', function (Blueprint $table) {
            $table->dropColumn(['title_i18n', 'subtitle_i18n', 'button_text_i18n']);
        });
        Schema::table('homepage_banners', function (Blueprint $table) {
            $table->dropColumn(['alt_text_i18n']);
        });
    }
};
