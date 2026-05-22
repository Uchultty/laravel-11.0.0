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
        Schema::table('barang_masuk', function (Blueprint $table) {
            $table->dropForeign(['barang_id']);
        });

        Schema::table('barang', function (Blueprint $table) {
            $table->renameColumn('id', 'id_barang');
        });

        Schema::table('barang_masuk', function (Blueprint $table) {
            $table->renameColumn('id', 'id_barang_masuk');
        });

        Schema::table('barang_masuk', function (Blueprint $table) {
            $table->foreign('barang_id')->references('id_barang')->on('barang')->onDelete('cascade');
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

        Schema::table('barang_masuk', function (Blueprint $table) {
            $table->renameColumn('id_barang_masuk', 'id');
        });

        Schema::table('barang', function (Blueprint $table) {
            $table->renameColumn('id_barang', 'id');
        });

        Schema::table('barang_masuk', function (Blueprint $table) {
            $table->foreign('barang_id')->references('id')->on('barang')->onDelete('cascade');
        });
    }
};
