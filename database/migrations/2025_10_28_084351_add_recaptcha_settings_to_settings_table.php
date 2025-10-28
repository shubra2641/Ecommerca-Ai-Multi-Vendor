<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->boolean('recaptcha_enabled')->default(false)->after('enable_external_payment_redirect');
            $table->string('recaptcha_site_key')->nullable()->after('recaptcha_enabled');
            $table->string('recaptcha_secret_key')->nullable()->after('recaptcha_site_key');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn(['recaptcha_enabled', 'recaptcha_site_key', 'recaptcha_secret_key']);
        });
    }
};
