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
        Schema::table('barang_dalam_proses', function (Blueprint $table) {
            $table->string('id_barang_keluar', 20)->nullable()->after('id_customer');
            $table->foreign('id_barang_keluar')->references('id_barang_keluar')->on('barang_keluar')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('barang_dalam_proses', function (Blueprint $table) {
            $table->dropForeign(['id_barang_keluar']);
            $table->dropColumn('id_barang_keluar');
        });
    }
};
