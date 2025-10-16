<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Drop old tables if exist
        foreach (['shipping_group_locations', 'shipping_groups'] as $table) {
            if (Schema::hasTable($table)) {
                Schema::drop($table);
            }
        }
        // Remove old columns from orders if exist
        Schema::table('orders', function (Blueprint $table) {
            $cols = ['shipping_price', 'shipping_group_id', 'shipping_estimated_days'];
            foreach ($cols as $c) {
                if (Schema::hasColumn('orders', $c)) {
                    $table->dropColumn($c);
                }
            }
        });
    }

    public function down()
    {
        // No rollback (intentionally destructive)
    }
};
