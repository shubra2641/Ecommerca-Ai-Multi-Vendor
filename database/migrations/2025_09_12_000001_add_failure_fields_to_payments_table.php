<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            if (! Schema::hasColumn('payments', 'failure_reason')) {
                $table->string('failure_reason')->nullable()->after('status');
            }
            if (! Schema::hasColumn('payments', 'failed_at')) {
                $table->timestamp('failed_at')->nullable()->after('failure_reason');
            }
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            if (Schema::hasColumn('payments', 'failed_at')) {
                $table->dropColumn('failed_at');
            }
            if (Schema::hasColumn('payments', 'failure_reason')) {
                $table->dropColumn('failure_reason');
            }
        });
    }
};
