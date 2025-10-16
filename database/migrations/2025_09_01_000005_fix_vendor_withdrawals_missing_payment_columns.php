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
                if (! Schema::hasColumn('vendor_withdrawals', 'payment_method')) {
                    // place after admin_note if column exists, else just add
                    $table->string('payment_method')->nullable()->after('admin_note');
                }
                if (! Schema::hasColumn('vendor_withdrawals', 'proof_path')) {
                    $table->string('proof_path')->nullable()->after('admin_note');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('vendor_withdrawals')) {
            Schema::table('vendor_withdrawals', function (Blueprint $table) {
                // Don't drop in down to avoid accidental prod data loss; keep idempotent.
            });
        }
    }
};
