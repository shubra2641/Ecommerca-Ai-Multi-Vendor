<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('product_interests', function (Blueprint $table) {
            $table->string('unsubscribe_token', 80)->nullable()->after('ip_address')->index();
            $table->timestamp('unsubscribed_at')->nullable()->after('unsubscribe_token');
            $table->timestamp('last_mail_at')->nullable()->after('notified_at');
        });
    }

    public function down(): void
    {
        Schema::table('product_interests', function (Blueprint $table) {
            $table->dropColumn(['unsubscribe_token', 'unsubscribed_at', 'last_mail_at']);
        });
    }
};
