<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (Schema::hasTable('payouts')) {
            Schema::table('payouts', function (Blueprint $table) {
                if (! Schema::hasColumn('payouts', 'proof_path')) {
                    $table->string('proof_path')->nullable()->after('admin_note');
                }
            });
        }

        if (Schema::hasTable('settings')) {
            Schema::table('settings', function (Blueprint $table) {
                if (! Schema::hasColumn('settings', 'min_withdrawal_amount')) {
                    $table->decimal('min_withdrawal_amount', 10, 2)->nullable()->after('maintenance_reopen_at');
                }
            });

            Schema::table('settings', function (Blueprint $table) {
                if (! Schema::hasColumn('settings', 'withdrawal_gateways')) {
                    $table->json('withdrawal_gateways')->nullable()->after('min_withdrawal_amount');
                }
                if (! Schema::hasColumn('settings', 'withdrawal_commission_enabled')) {
                    $table->boolean('withdrawal_commission_enabled')->default(false)->after('withdrawal_gateways');
                }
                if (! Schema::hasColumn('settings', 'withdrawal_commission_rate')) {
                    $table->decimal('withdrawal_commission_rate', 5, 2)->default(0)->after('withdrawal_commission_enabled');
                }
            });
        }
    }

    public function down()
    {
        Schema::table('payouts', function (Blueprint $table) {
            if (Schema::hasColumn('payouts', 'proof_path')) {
                $table->dropColumn('proof_path');
            }
        });

        Schema::table('settings', function (Blueprint $table) {
            if (Schema::hasColumn('settings', 'withdrawal_gateways')) {
                $table->dropColumn('withdrawal_gateways');
            }
            if (Schema::hasColumn('settings', 'min_withdrawal_amount')) {
                $table->dropColumn('min_withdrawal_amount');
            }
            if (Schema::hasColumn('settings', 'withdrawal_commission_enabled')) {
                $table->dropColumn('withdrawal_commission_enabled');
            }
            if (Schema::hasColumn('settings', 'withdrawal_commission_rate')) {
                $table->dropColumn('withdrawal_commission_rate');
            }
        });
    }
};
