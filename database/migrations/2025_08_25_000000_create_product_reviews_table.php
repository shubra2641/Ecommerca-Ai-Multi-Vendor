<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('product_reviews', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('product_id')->index();
            $table->unsignedBigInteger('user_id')->nullable()->index();
            $table->tinyInteger('rating')->unsigned();
            $table->string('title')->nullable();
            $table->text('body')->nullable();
            $table->unsignedBigInteger('order_id')->nullable()->index();
            $table->boolean('approved')->default(false)->index();
            $table->timestamps();

            // avoid hard foreign keys to keep compatibility with unknown orders table
        });
    }

    public function down()
    {
        Schema::dropIfExists('product_reviews');
    }
};
