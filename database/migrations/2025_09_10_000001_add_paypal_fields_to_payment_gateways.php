<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('payment_gateways', function (Blueprint $table) {
            if (! Schema::hasColumn('payment_gateways', 'paypal_client_id')) {
                $table->string('paypal_client_id')->nullable();
            }
            if (! Schema::hasColumn('payment_gateways', 'paypal_secret')) {
                $table->text('paypal_secret')->nullable();
            }
            if (! Schema::hasColumn('payment_gateways', 'paypal_mode')) {
                $table->string('paypal_mode')->default('sandbox');
            }
        });
    }

    public function down()
    {
        Schema::table('payment_gateways', function (Blueprint $table) {
            $table->dropColumn(['paypal_client_id', 'paypal_secret', 'paypal_mode']);
        });
    }
};
