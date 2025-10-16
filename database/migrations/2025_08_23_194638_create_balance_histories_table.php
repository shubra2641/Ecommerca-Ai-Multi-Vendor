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
        Schema::create('balance_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('admin_id')->nullable()->constrained('users')->onDelete('set null');
            $table->enum('type', ['credit', 'debit', 'adjustment', 'refund', 'bonus', 'penalty']);
            $table->decimal('amount', 10, 2);
            $table->decimal('previous_balance', 10, 2);
            $table->decimal('new_balance', 10, 2);
            $table->text('note')->nullable();
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->string('reference_type')->nullable();
            $table->timestamps();

            // Indexes for better performance
            $table->index(['user_id', 'created_at']);
            $table->index(['type', 'created_at']);
            $table->index(['reference_id', 'reference_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('balance_histories');
    }
};
