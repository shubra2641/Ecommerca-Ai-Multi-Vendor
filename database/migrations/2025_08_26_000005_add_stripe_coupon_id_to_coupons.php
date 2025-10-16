<?php

use Illuminate\Database\Migrations\Migration;

// migration sanitized - provide a guarded no-op class so the migrator can require the file safely.
if (! class_exists('AddStripeCouponIdToCouponsC')) {
    class AddStripeCouponIdToCouponsC extends Migration
    {
        public function up()
        {
            // no-op (duplicate handled by 2025_08_27_010000_add_stripe_coupon_id_to_coupons_unique.php)
        }

        public function down()
        {
            // no-op
        }
    }
}
