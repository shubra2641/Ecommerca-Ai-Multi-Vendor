<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // Make order_id nullable to allow creating Payments before Orders (for redirect flows)
        // Use a raw statement for MySQL. SQLite (used in tests) does not support MODIFY; skip there.
        $driver = DB::connection()->getDriverName();
        if ($driver === 'sqlite') {
            // SQLite used for tests doesn't support MODIFY; no-op to keep tests fast and avoid complex table rebuild.
            return;
        }

        // MySQL / compatible drivers: alter the column directly.
        DB::statement('ALTER TABLE `payments` MODIFY `order_id` BIGINT UNSIGNED NULL');
    }

    public function down()
    {
        // Revert: set any NULL order_id to 0 then make NOT NULL
        $driver = DB::connection()->getDriverName();
        if ($driver === 'sqlite') {
            // SQLite: nothing to revert (up was a no-op), but ensure any test fixtures won't rely on NULLs.
            DB::statement('UPDATE `payments` SET `order_id` = 0 WHERE `order_id` IS NULL');

            return;
        }

        DB::statement('UPDATE `payments` SET `order_id` = 0 WHERE `order_id` IS NULL');
        DB::statement('ALTER TABLE `payments` MODIFY `order_id` BIGINT UNSIGNED NOT NULL');
    }
};
