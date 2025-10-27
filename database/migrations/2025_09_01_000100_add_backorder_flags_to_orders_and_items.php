<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            if (! Schema::hasColumn('orders', 'has_backorder')) {
                $table->boolean('has_backorder')->default(false)->after('status');
            }
        });

        Schema::table('order_items', function (Blueprint $table) {
            if (! Schema::hasColumn('order_items', 'is_backorder')) {
                $table->boolean('is_backorder')->default(false)->after('meta');
            }
        });
    }

    public function down()
    {
        Schema::table('order_items', function (Blueprint $table) {
            if (Schema::hasColumn('order_items', 'is_backorder')) {
                $table->dropColumn('is_backorder');
            }
        });

        Schema::table('orders', function (Blueprint $table) {
            if (Schema::hasColumn('orders', 'has_backorder')) {
                $table->dropColumn('has_backorder');
            }
        });
    }
};
