<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            if (! Schema::hasColumn('order_items', 'purchased_at')) {
                $table->timestamp('purchased_at')->nullable()->after('price');
            }
            if (! Schema::hasColumn('order_items', 'refund_expires_at')) {
                $table->timestamp('refund_expires_at')->nullable()->after('purchased_at');
            }
            if (! Schema::hasColumn('order_items', 'return_requested')) {
                $table->boolean('return_requested')->default(false)->after('refund_expires_at');
            }
            if (! Schema::hasColumn('order_items', 'return_status')) {
                $table->string('return_status', 50)->nullable()->after('return_requested');
            }
            if (! Schema::hasColumn('order_items', 'return_reason')) {
                $table->text('return_reason')->nullable()->after('return_status');
            }
        });
    }

    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            if (Schema::hasColumn('order_items', 'return_reason')) {
                $table->dropColumn('return_reason');
            }
            if (Schema::hasColumn('order_items', 'return_status')) {
                $table->dropColumn('return_status');
            }
            if (Schema::hasColumn('order_items', 'return_requested')) {
                $table->dropColumn('return_requested');
            }
            if (Schema::hasColumn('order_items', 'refund_expires_at')) {
                $table->dropColumn('refund_expires_at');
            }
            if (Schema::hasColumn('order_items', 'purchased_at')) {
                $table->dropColumn('purchased_at');
            }
        });
    }
};
