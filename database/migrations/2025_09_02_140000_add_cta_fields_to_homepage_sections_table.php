<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('homepage_sections', function (Blueprint $table) {
            $table->boolean('cta_enabled')->default(true)->after('item_limit');
            $table->string('cta_url')->nullable()->after('cta_enabled');
            $table->json('cta_label_i18n')->nullable()->after('cta_url');
        });
    }

    public function down(): void
    {
        Schema::table('homepage_sections', function (Blueprint $table) {
            $table->dropColumn(['cta_enabled', 'cta_url', 'cta_label_i18n']);
        });
    }
};
