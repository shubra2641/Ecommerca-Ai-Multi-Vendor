<?php

namespace Database\Migrations;

// Disabled backup of migration
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStripeCouponIdToCouponsB extends Migration
{
    public function up()
    {
        if (Schema::hasTable('coupons')) {
            Schema::table('coupons', function (Blueprint $table) {
                if (! Schema::hasColumn('coupons', 'stripe_coupon_id')) {
                    $table->string('stripe_coupon_id')->nullable()->after('code');
                }
            });
        }
    }

    public function down()
    {
        Schema::table('coupons', function (Blueprint $table) {
            if (Schema::hasColumn('coupons', 'stripe_coupon_id')) {
                $table->dropColumn('stripe_coupon_id');
            }
        });
    }
}
