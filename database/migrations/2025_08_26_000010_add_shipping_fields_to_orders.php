<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->decimal('shipping_price', 12, 2)->nullable()->after('total');
            $table->unsignedBigInteger('shipping_group_id')->nullable()->after('shipping_price');
            $table->integer('shipping_estimated_days')->nullable()->after('shipping_group_id');
        });
    }

    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['shipping_price', 'shipping_group_id', 'shipping_estimated_days']);
        });
    }
};
