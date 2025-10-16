<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('payment_gateways', function (Blueprint $table) {
            if (! Schema::hasColumn('payment_gateways', 'stripe_publishable')) {
                $table->string('stripe_publishable')->nullable();
            }
            if (! Schema::hasColumn('payment_gateways', 'stripe_secret')) {
                $table->string('stripe_secret')->nullable();
            }
            if (! Schema::hasColumn('payment_gateways', 'paypal_client_id')) {
                $table->string('paypal_client_id')->nullable();
            }
            if (! Schema::hasColumn('payment_gateways', 'paypal_secret')) {
                $table->string('paypal_secret')->nullable();
            }
        });
    }

    public function down()
    {
        Schema::table('payment_gateways', function (Blueprint $table) {
            $table->dropColumn(['stripe_publishable', 'stripe_secret', 'paypal_client_id', 'paypal_secret']);
        });
    }
};
