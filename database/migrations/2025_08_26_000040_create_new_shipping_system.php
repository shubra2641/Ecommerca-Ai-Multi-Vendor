<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('shipping_zones', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('code')->nullable()->unique();
            $table->boolean('active')->default(true);
            $table->timestamps();
        });

        Schema::create('shipping_rules', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('zone_id')->index();
            $table->unsignedBigInteger('country_id')->nullable()->index();
            $table->unsignedBigInteger('governorate_id')->nullable()->index();
            $table->unsignedBigInteger('city_id')->nullable()->index();
            $table->decimal('price', 12, 2)->nullable();
            $table->integer('estimated_days')->nullable();
            $table->boolean('active')->default(true);
            $table->timestamps();
            $table->foreign('zone_id')->references('id')->on('shipping_zones')->cascadeOnDelete();
            $table->unique(['zone_id', 'country_id', 'governorate_id', 'city_id'], 'shipping_rules_unique_scope');
        });

        // Add new zone reference columns to orders if not exist (shipping_price may have been dropped then re-added logic handled elsewhere)
        if (Schema::hasTable('orders')) {
            Schema::table('orders', function (Blueprint $table) {
                if (! Schema::hasColumn('orders', 'shipping_price')) {
                    $table->decimal('shipping_price', 12, 2)->nullable()->after('items_subtotal');
                }
                if (! Schema::hasColumn('orders', 'shipping_zone_id')) {
                    $table->unsignedBigInteger('shipping_zone_id')->nullable()->after('shipping_price');
                }
                if (! Schema::hasColumn('orders', 'shipping_estimated_days')) {
                    $table->integer('shipping_estimated_days')->nullable()->after('shipping_zone_id');
                }
            });
        }
    }

    public function down()
    {
        if (Schema::hasTable('orders')) {
            Schema::table('orders', function (Blueprint $table) {
                if (Schema::hasColumn('orders', 'shipping_zone_id')) {
                    $table->dropColumn('shipping_zone_id');
                }
                if (Schema::hasColumn('orders', 'shipping_estimated_days')) {
                    $table->dropColumn('shipping_estimated_days');
                }
            });
        }
        Schema::dropIfExists('shipping_rules');
        Schema::dropIfExists('shipping_zones');
    }
};
