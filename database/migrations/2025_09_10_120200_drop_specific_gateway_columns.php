<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payment_gateways', function (Blueprint $table) {
            $cols = [
                'stripe_publishable_key', 'stripe_secret_key', 'stripe_webhook_secret', 'stripe_mode',
                'paypal_client_id', 'paypal_secret', 'paypal_mode', 'paypal_webhook_id',
            ];
            foreach ($cols as $col) {
                if (Schema::hasColumn('payment_gateways', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }

    public function down(): void
    {
        Schema::table('payment_gateways', function (Blueprint $table) {
            // Re-create columns (kept nullable for safe rollback)
            if (! Schema::hasColumn('payment_gateways', 'stripe_publishable_key')) {
                $table->string('stripe_publishable_key')->nullable();
            }
            if (! Schema::hasColumn('payment_gateways', 'stripe_secret_key')) {
                $table->text('stripe_secret_key')->nullable();
            }
            if (! Schema::hasColumn('payment_gateways', 'stripe_webhook_secret')) {
                $table->text('stripe_webhook_secret')->nullable();
            }
            if (! Schema::hasColumn('payment_gateways', 'stripe_mode')) {
                $table->string('stripe_mode')->default('test');
            }
            if (! Schema::hasColumn('payment_gateways', 'paypal_client_id')) {
                $table->string('paypal_client_id')->nullable();
            }
            if (! Schema::hasColumn('payment_gateways', 'paypal_secret')) {
                $table->text('paypal_secret')->nullable();
            }
            if (! Schema::hasColumn('payment_gateways', 'paypal_mode')) {
                $table->string('paypal_mode')->default('sandbox');
            }
            if (! Schema::hasColumn('payment_gateways', 'paypal_webhook_id')) {
                $table->string('paypal_webhook_id')->nullable();
            }
        });
    }
};
