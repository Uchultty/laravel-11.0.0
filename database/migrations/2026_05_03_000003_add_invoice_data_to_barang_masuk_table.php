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
        if (Schema::hasTable('barang_masuk') && !Schema::hasColumn('barang_masuk', 'invoice_data')) {
            Schema::table('barang_masuk', function (Blueprint $table) {
                $table->json('invoice_data')->nullable()->comment('JSON data for invoice: number, date, and items');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('barang_masuk') && Schema::hasColumn('barang_masuk', 'invoice_data')) {
            Schema::table('barang_masuk', function (Blueprint $table) {
                $table->dropColumn('invoice_data');
            });
        }
    }
};
