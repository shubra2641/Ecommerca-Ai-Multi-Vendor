<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('payments', function (Blueprint $table) {
            // Timestamps
            if (! Schema::hasColumn('payments', 'paid_at')) {
                $table->timestamp('paid_at')->nullable()->after('status');
            }
            if (! Schema::hasColumn('payments', 'completed_at')) {
                $table->timestamp('completed_at')->nullable()->after('paid_at');
            }
            if (! Schema::hasColumn('payments', 'failed_at')) {
                $table->timestamp('failed_at')->nullable()->after('completed_at');
            }
            if (! Schema::hasColumn('payments', 'cancelled_at')) {
                $table->timestamp('cancelled_at')->nullable()->after('failed_at');
            }
            if (! Schema::hasColumn('payments', 'refunded_at')) {
                $table->timestamp('refunded_at')->nullable()->after('cancelled_at');
            }

            // Refund / gateway fields
            if (! Schema::hasColumn('payments', 'refunded_amount')) {
                $table->decimal('refunded_amount', 12, 2)->nullable()->after('refunded_at');
            }
            if (! Schema::hasColumn('payments', 'gateway_reference')) {
                $table->string('gateway_reference')->nullable()->after('transaction_id');
            }
            if (! Schema::hasColumn('payments', 'gateway_fee')) {
                $table->decimal('gateway_fee', 12, 2)->nullable()->after('gateway_reference');
            }
            if (! Schema::hasColumn('payments', 'failure_reason')) {
                $table->string('failure_reason')->nullable()->after('gateway_fee');
            }
            if (! Schema::hasColumn('payments', 'reference_number')) {
                $table->string('reference_number')->nullable()->after('failure_reason');
            }
        });
    }

    public function down()
    {
        Schema::table('payments', function (Blueprint $table) {
            $columns = [
                'paid_at', 'completed_at', 'failed_at', 'cancelled_at', 'refunded_at',
                'refunded_amount', 'gateway_reference', 'gateway_fee', 'failure_reason', 'reference_number',
            ];

            foreach ($columns as $col) {
                if (Schema::hasColumn('payments', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
