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
        Schema::table('barang', function (Blueprint $table) {
            if (! Schema::hasColumn('barang', 'quantity')) {
                $table->integer('quantity')->default(0)->after('id_jenis_barang');
            }

            if (! Schema::hasColumn('barang', 'stok_minimum')) {
                $table->integer('stok_minimum')->default(10)->after('quantity');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('barang', function (Blueprint $table) {
            if (Schema::hasColumn('barang', 'stok_minimum')) {
                $table->dropColumn('stok_minimum');
            }

            if (Schema::hasColumn('barang', 'quantity')) {
                $table->dropColumn('quantity');
            }
        });
    }
};
