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
        if (! Schema::hasTable('product_material')) {
            return;
        }

        Schema::table('product_material', function (Blueprint $table) {
            if (Schema::hasColumn('product_material', 'quantity')) {
                $table->dropColumn('quantity');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_material', function (Blueprint $table) {
            if (! Schema::hasColumn('product_material', 'quantity')) {
                $table->decimal('quantity', 12, 3)->default(1)->after('id_material');
            }
        });
    }
};
