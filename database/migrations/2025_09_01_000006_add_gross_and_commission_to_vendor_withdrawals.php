<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('vendor_withdrawals')) {
            Schema::table('vendor_withdrawals', function (Blueprint $table) {
                if (! Schema::hasColumn('vendor_withdrawals', 'gross_amount')) {
                    $table->decimal('gross_amount', 12, 2)->nullable()->after('amount');
                }
                if (! Schema::hasColumn('vendor_withdrawals', 'commission_amount')) {
                    $table->decimal('commission_amount', 12, 2)->nullable()->after('gross_amount');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('vendor_withdrawals')) {
            Schema::table('vendor_withdrawals', function (Blueprint $table) {
                if (Schema::hasColumn('vendor_withdrawals', 'commission_amount')) {
                    $table->dropColumn('commission_amount');
                }
                if (Schema::hasColumn('vendor_withdrawals', 'gross_amount')) {
                    $table->dropColumn('gross_amount');
                }
            });
        }
    }
};
