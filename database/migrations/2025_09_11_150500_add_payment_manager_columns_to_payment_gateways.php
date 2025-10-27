<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payment_gateways', function (Blueprint $table) {
            if (! Schema::hasColumn('payment_gateways', 'fees')) {
                $table->json('fees')->nullable()->after('config');
            }
            if (! Schema::hasColumn('payment_gateways', 'supported_currencies')) {
                $table->json('supported_currencies')->nullable()->after('fees');
            }
            if (! Schema::hasColumn('payment_gateways', 'supported_methods')) {
                $table->json('supported_methods')->nullable()->after('supported_currencies');
            }
            if (! Schema::hasColumn('payment_gateways', 'maintenance_mode')) {
                $table->boolean('maintenance_mode')->default(false)->after('supported_methods');
            }
        });
    }

    public function down(): void
    {
        Schema::table('payment_gateways', function (Blueprint $table) {
            foreach (['fees', 'supported_currencies', 'supported_methods', 'maintenance_mode'] as $col) {
                if (Schema::hasColumn('payment_gateways', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
