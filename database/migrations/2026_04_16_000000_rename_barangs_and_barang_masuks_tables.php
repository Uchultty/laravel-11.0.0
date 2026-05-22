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
        Schema::table('barang_masuks', function (Blueprint $table) {
            $table->dropForeign(['barang_id']);
        });

        Schema::rename('barangs', 'barang');
        Schema::rename('barang_masuks', 'barang_masuk');

        Schema::table('barang_masuk', function (Blueprint $table) {
            $table->foreign('barang_id')->references('id')->on('barang')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('barang_masuk', function (Blueprint $table) {
            $table->dropForeign(['barang_id']);
        });

        Schema::rename('barang_masuk', 'barang_masuks');
        Schema::rename('barang', 'barangs');

        Schema::table('barang_masuks', function (Blueprint $table) {
            $table->foreign('barang_id')->references('id')->on('barangs')->onDelete('cascade');
        });
    }
};
