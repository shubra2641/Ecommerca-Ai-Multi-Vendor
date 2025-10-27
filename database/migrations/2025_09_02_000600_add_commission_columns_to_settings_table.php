<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            if (! Schema::hasColumn('settings', 'commission_mode')) {
                $table->string('commission_mode', 20)->default('flat')->after('withdrawal_commission_rate');
            }
            if (! Schema::hasColumn('settings', 'commission_flat_rate')) {
                $table->decimal('commission_flat_rate', 5, 2)->nullable()->after('commission_mode');
            }
        });
    }

    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            if (Schema::hasColumn('settings', 'commission_flat_rate')) {
                $table->dropColumn('commission_flat_rate');
            }
            if (Schema::hasColumn('settings', 'commission_mode')) {
                $table->dropColumn('commission_mode');
            }
        });
    }
};
