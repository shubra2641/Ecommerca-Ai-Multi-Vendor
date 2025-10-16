<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\App;

return new class extends Migration
{
    public function up(): void
    {
        // Avoid dropping these columns in testing or when using sqlite (test DB compatibility)
        $driver = DB::connection()->getDriverName();
        if (App::environment('testing') || $driver === 'sqlite') {
            return;
        }

        if (Schema::hasTable('payment_gateways')) {
            Schema::table('payment_gateways', function (Blueprint $table) {
                $drop = [
                    'api_key', 'secret_key', 'public_key', 'merchant_id', 'webhook_secret',
                    'sandbox_mode', 'additional_config', 'fees', 'supported_currencies', 'supported_methods', 'maintenance_mode',
                ];
                foreach ($drop as $col) {
                    if (Schema::hasColumn('payment_gateways', $col)) {
                        $table->dropColumn($col);
                    }
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('payment_gateways')) {
            Schema::table('payment_gateways', function (Blueprint $table) {
                // Recreate columns as nullable simple types (original types may have differed)
                if (! Schema::hasColumn('payment_gateways', 'api_key')) {
                    $table->text('api_key')->nullable();
                }
                if (! Schema::hasColumn('payment_gateways', 'secret_key')) {
                    $table->text('secret_key')->nullable();
                }
                if (! Schema::hasColumn('payment_gateways', 'public_key')) {
                    $table->text('public_key')->nullable();
                }
                if (! Schema::hasColumn('payment_gateways', 'merchant_id')) {
                    $table->string('merchant_id')->nullable();
                }
                if (! Schema::hasColumn('payment_gateways', 'webhook_secret')) {
                    $table->text('webhook_secret')->nullable();
                }
                if (! Schema::hasColumn('payment_gateways', 'sandbox_mode')) {
                    $table->boolean('sandbox_mode')->default(true);
                }
                if (! Schema::hasColumn('payment_gateways', 'additional_config')) {
                    $table->json('additional_config')->nullable();
                }
                if (! Schema::hasColumn('payment_gateways', 'fees')) {
                    $table->json('fees')->nullable();
                }
                if (! Schema::hasColumn('payment_gateways', 'supported_currencies')) {
                    $table->json('supported_currencies')->nullable();
                }
                if (! Schema::hasColumn('payment_gateways', 'supported_methods')) {
                    $table->json('supported_methods')->nullable();
                }
                if (! Schema::hasColumn('payment_gateways', 'maintenance_mode')) {
                    $table->boolean('maintenance_mode')->default(false);
                }
            });
        }
    }
};
