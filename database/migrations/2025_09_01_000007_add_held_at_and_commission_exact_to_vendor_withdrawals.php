<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('vendor_withdrawals', function (Blueprint $table) {
            if (! Schema::hasColumn('vendor_withdrawals', 'held_at')) {
                $table->timestamp('held_at')->nullable()->after('approved_at');
            }
            if (! Schema::hasColumn('vendor_withdrawals', 'commission_amount_exact')) {
                $table->decimal('commission_amount_exact', 12, 4)->nullable()->after('commission_amount');
            }
        });
    }

    public function down(): void
    {
        Schema::table('vendor_withdrawals', function (Blueprint $table) {
            if (Schema::hasColumn('vendor_withdrawals', 'commission_amount_exact')) {
                $table->dropColumn('commission_amount_exact');
            }
            if (Schema::hasColumn('vendor_withdrawals', 'held_at')) {
                $table->dropColumn('held_at');
            }
        });
    }
};
