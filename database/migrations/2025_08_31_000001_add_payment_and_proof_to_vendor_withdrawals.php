<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (Schema::hasTable('vendor_withdrawals')) {
            Schema::table('vendor_withdrawals', function (Blueprint $table) {
                if (! Schema::hasColumn('vendor_withdrawals', 'proof_path')) {
                    $table->string('proof_path')->nullable()->after('admin_note');
                }
            });
            Schema::table('vendor_withdrawals', function (Blueprint $table) {
                if (! Schema::hasColumn('vendor_withdrawals', 'payment_method')) {
                    $table->string('payment_method')->nullable()->after('proof_path');
                }
            });
        }
    }

    public function down()
    {
        Schema::table('vendor_withdrawals', function (Blueprint $table) {
            if (Schema::hasColumn('vendor_withdrawals', 'payment_method')) {
                $table->dropColumn('payment_method');
            }
            if (Schema::hasColumn('vendor_withdrawals', 'proof_path')) {
                $table->dropColumn('proof_path');
            }
        });
    }
};
