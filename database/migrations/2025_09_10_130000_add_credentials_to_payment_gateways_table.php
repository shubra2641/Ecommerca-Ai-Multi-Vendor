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
        Schema::table('payment_gateways', function (Blueprint $table) {
            // Add encrypted credentials fields
            $table->text('api_key')->nullable()->after('config');
            $table->text('secret_key')->nullable()->after('api_key');
            $table->text('public_key')->nullable()->after('secret_key');
            $table->text('merchant_id')->nullable()->after('public_key');
            $table->text('webhook_secret')->nullable()->after('merchant_id');
            $table->boolean('sandbox_mode')->default(true)->after('webhook_secret');
            $table->text('additional_config')->nullable()->after('sandbox_mode')->comment('JSON field for additional gateway-specific configuration');

            // Add index for better performance
            $table->index(['slug', 'enabled']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payment_gateways', function (Blueprint $table) {
            $table->dropIndex(['slug', 'enabled']);
            $table->dropColumn([
                'api_key',
                'secret_key',
                'public_key',
                'merchant_id',
                'webhook_secret',
                'sandbox_mode',
                'additional_config',
            ]);
        });
    }
};
