<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('payment_gateways', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('driver')->nullable();
            $table->json('config')->nullable();
            $table->boolean('enabled')->default(false);
            $table->boolean('requires_transfer_image')->default(false);
            $table->text('transfer_instructions')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('payment_gateways');
    }
};
