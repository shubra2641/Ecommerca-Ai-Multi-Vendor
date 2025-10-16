<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('countries', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('iso_code')->nullable();
            $table->boolean('active')->default(true);
            $table->timestamps();
        });

        Schema::create('governorates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('country_id')->constrained('countries')->cascadeOnDelete();
            $table->string('name');
            $table->boolean('active')->default(true);
            $table->timestamps();
        });

        Schema::create('cities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('governorate_id')->constrained('governorates')->cascadeOnDelete();
            $table->string('name');
            $table->boolean('active')->default(true);
            $table->timestamps();
        });

        Schema::create('shipping_groups', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->decimal('default_price', 12, 2)->nullable();
            $table->integer('estimated_days')->nullable();
            $table->boolean('active')->default(true);
            $table->timestamps();
        });

        Schema::create('shipping_group_locations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shipping_group_id')->constrained('shipping_groups')->cascadeOnDelete();
            $table->foreignId('country_id')->nullable()->constrained('countries')->nullOnDelete();
            $table->foreignId('governorate_id')->nullable()->constrained('governorates')->nullOnDelete();
            $table->foreignId('city_id')->nullable()->constrained('cities')->nullOnDelete();
            $table->decimal('price', 12, 2)->nullable();
            $table->integer('estimated_days')->nullable();
            $table->timestamps();
            $table->index(['country_id', 'governorate_id', 'city_id'], 's_gl_loc_idx');
        });
    }

    public function down()
    {
        Schema::dropIfExists('shipping_group_locations');
        Schema::dropIfExists('shipping_groups');
        Schema::dropIfExists('cities');
        Schema::dropIfExists('governorates');
        Schema::dropIfExists('countries');
    }
};
