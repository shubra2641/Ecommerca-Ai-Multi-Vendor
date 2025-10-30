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
        if (!Schema::hasTable('settings')) {
            return;
        }

        $hasExternal = Schema::hasColumn('settings', 'enable_external_payment_redirect');

        Schema::table('settings', function (Blueprint $table) use ($hasExternal) {
            if (!Schema::hasColumn('settings', 'recaptcha_enabled')) {
                $col = $table->boolean('recaptcha_enabled')->default(false);
                if ($hasExternal) {
                    $col->after('enable_external_payment_redirect');
                }
            }

            if (!Schema::hasColumn('settings', 'recaptcha_site_key')) {
                $col2 = $table->string('recaptcha_site_key')->nullable();
                if (Schema::hasColumn('settings', 'recaptcha_enabled')) {
                    $col2->after('recaptcha_enabled');
                }
            }

            if (!Schema::hasColumn('settings', 'recaptcha_secret_key')) {
                $col3 = $table->string('recaptcha_secret_key')->nullable();
                if (Schema::hasColumn('settings', 'recaptcha_site_key')) {
                    $col3->after('recaptcha_site_key');
                }
            }
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
