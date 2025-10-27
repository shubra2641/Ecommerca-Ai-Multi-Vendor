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
        if (Schema::hasTable('settings') && ! Schema::hasColumn('settings', 'enable_external_payment_redirect')) {
            Schema::table('settings', function (Blueprint $table) {
                $table->boolean('enable_external_payment_redirect')->default(false)->after('ai_openai_api_key');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('settings') && Schema::hasColumn('settings', 'enable_external_payment_redirect')) {
            Schema::table('settings', function (Blueprint $table) {
                $table->dropColumn('enable_external_payment_redirect');
            });
        }
    }
};
