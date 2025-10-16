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
                if (! Schema::hasColumn('vendor_withdrawals', 'reference')) {
                    $table->string('reference', 32)->nullable();
                    $table->index('reference');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('vendor_withdrawals')) {
            Schema::table('vendor_withdrawals', function (Blueprint $table) {
                if (Schema::hasColumn('vendor_withdrawals', 'reference')) {
                    $table->dropIndex(['reference']);
                    $table->dropColumn('reference');
                }
            });
        }
    }
};
