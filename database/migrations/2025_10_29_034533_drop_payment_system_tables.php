<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::dropIfExists('payment_attachments');
        Schema::dropIfExists('payments');
        Schema::dropIfExists('payment_gateways');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No rollback as we're removing the system entirely
    }
};
