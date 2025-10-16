<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if (! Schema::hasColumn('products', 'refund_days')) {
                $table->integer('refund_days')->default(0)->after('active')->comment('Refund window in days; 0 = none');
            }
            if (! Schema::hasColumn('products', 'weight')) {
                $table->decimal('weight', 10, 2)->nullable()->after('refund_days');
            }
            if (! Schema::hasColumn('products', 'length')) {
                $table->decimal('length', 10, 2)->nullable()->after('weight');
            }
            if (! Schema::hasColumn('products', 'width')) {
                $table->decimal('width', 10, 2)->nullable()->after('length');
            }
            if (! Schema::hasColumn('products', 'height')) {
                $table->decimal('height', 10, 2)->nullable()->after('width');
            }
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if (Schema::hasColumn('products', 'height')) {
                $table->dropColumn('height');
            }
            if (Schema::hasColumn('products', 'width')) {
                $table->dropColumn('width');
            }
            if (Schema::hasColumn('products', 'length')) {
                $table->dropColumn('length');
            }
            if (Schema::hasColumn('products', 'weight')) {
                $table->dropColumn('weight');
            }
            if (Schema::hasColumn('products', 'refund_days')) {
                $table->dropColumn('refund_days');
            }
        });
    }
};
