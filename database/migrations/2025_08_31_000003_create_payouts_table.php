<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('payouts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('vendor_withdrawal_id');
            $table->unsignedBigInteger('user_id');
            $table->decimal('amount', 12, 2);
            $table->string('currency', 10)->nullable();
            $table->string('status')->default('pending'); // pending, executed, failed
            $table->text('admin_note')->nullable();
            $table->timestamp('executed_at')->nullable();
            $table->timestamps();

            $table->foreign('vendor_withdrawal_id')->references('id')->on('vendor_withdrawals')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('payouts');
    }
};
