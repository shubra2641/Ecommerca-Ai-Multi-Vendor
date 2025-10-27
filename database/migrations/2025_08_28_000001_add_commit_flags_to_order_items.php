<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            if (! Schema::hasColumn('order_items', 'committed')) {
                // add committed; prefer placing after is_backorder if it exists
                if (Schema::hasColumn('order_items', 'is_backorder')) {
                    $table->boolean('committed')->default(false)->after('is_backorder');
                } else {
                    $table->boolean('committed')->default(false);
                }
            }

            if (! Schema::hasColumn('order_items', 'restocked')) {
                // add restocked; prefer placing after committed if possible
                if (Schema::hasColumn('order_items', 'committed')) {
                    $table->boolean('restocked')->default(false)->after('committed');
                } else {
                    $table->boolean('restocked')->default(false);
                }
            }
        });
    }

    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            if (Schema::hasColumn('order_items', 'committed')) {
                $table->dropColumn('committed');
            }
            if (Schema::hasColumn('order_items', 'restocked')) {
                $table->dropColumn('restocked');
            }
        });
    }
};
