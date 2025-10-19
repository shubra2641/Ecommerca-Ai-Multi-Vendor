<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('product_variations', function (Blueprint $table) {
            if (! Schema::hasColumn('product_variations', 'name')) {
                $table->string('name')->nullable()->after('product_id');
            }
            if (! Schema::hasColumn('product_variations', 'name_translations')) {
                $table->json('name_translations')->nullable()->after('name');
            }
        });

        // Backfill names for existing rows based on attribute_data values
        try {
            if (Schema::hasColumn('product_variations', 'attribute_data')) {
                DB::table('product_variations')->orderBy('id')->chunk(200, function ($items) {
                    foreach ($items as $it) {
                        if (! $it->name) {
                            $attr = json_decode($it->attribute_data ?? '[]', true) ?: [];
                            if (is_array($attr) && ! empty($attr)) {
                                $label = collect($attr)->filter(fn ($v) => $v !== null && $v !== '')->map(fn ($v, $k) => ucfirst($k) . ': ' . $v)->join(' / ');
                                if ($label) {
                                    DB::table('product_variations')->where('id', $it->id)->update(['name' => $label, 'name_translations' => json_encode(['en' => $label])]);
                                }
                            }
                        }
                    }
                });
            }
        } catch (Throwable $e) {
            // ignore backfill errors
        }
    }

    public function down(): void
    {
        Schema::table('product_variations', function (Blueprint $table) {
            if (Schema::hasColumn('product_variations', 'name_translations')) {
                $table->dropColumn('name_translations');
            }
            if (Schema::hasColumn('product_variations', 'name')) {
                $table->dropColumn('name');
            }
        });
    }
};
