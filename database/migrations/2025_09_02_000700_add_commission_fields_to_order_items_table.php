<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            if (! Schema::hasColumn('order_items', 'vendor_commission_rate')) {
                $table->decimal('vendor_commission_rate', 5, 2)->nullable()->after('price');
            }
            if (! Schema::hasColumn('order_items', 'vendor_commission_amount')) {
                $table->decimal('vendor_commission_amount', 10, 2)->nullable()->after('vendor_commission_rate');
            }
            if (! Schema::hasColumn('order_items', 'vendor_earnings')) {
                $table->decimal('vendor_earnings', 10, 2)->nullable()->after('vendor_commission_amount');
            }
        });
    }

    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            if (Schema::hasColumn('order_items', 'vendor_earnings')) {
                $table->dropColumn('vendor_earnings');
            }
            if (Schema::hasColumn('order_items', 'vendor_commission_amount')) {
                $table->dropColumn('vendor_commission_amount');
            }
            if (Schema::hasColumn('order_items', 'vendor_commission_rate')) {
                $table->dropColumn('vendor_commission_rate');
            }
        });
    }
};
