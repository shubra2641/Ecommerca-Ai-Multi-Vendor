<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if (! Schema::hasColumn('products', 'vendor_id')) {
                $table->unsignedBigInteger('vendor_id')->nullable()->after('id');
                $table->index('vendor_id');
            }
        });

        if (! Schema::hasTable('vendor_withdrawals')) {
            Schema::create('vendor_withdrawals', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id');
                $table->decimal('amount', 12, 2);
                $table->string('currency', 8)->default('USD');
                $table->string('status')->default('pending');
                $table->text('notes')->nullable();
                $table->text('admin_note')->nullable();
                $table->timestamps();
                $table->timestamp('approved_at')->nullable();

                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            });
        }
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if (Schema::hasColumn('products', 'vendor_id')) {
                $table->dropForeign(['vendor_id']);
                $table->dropIndex(['vendor_id']);
                $table->dropColumn('vendor_id');
            }
        });

        Schema::dropIfExists('vendor_withdrawals');
    }
};
