<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('barang_keluar', function (Blueprint $table) {
            if (! Schema::hasColumn('barang_keluar', 'id_barang_proses')) {
                $table->unsignedBigInteger('id_barang_proses')->nullable()->after('id_user');
                $table->foreign('id_barang_proses')->references('id_barang_proses')->on('barang_dalam_proses')->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('barang_keluar', function (Blueprint $table) {
            if (Schema::hasColumn('barang_keluar', 'id_barang_proses')) {
                $table->dropForeign(['id_barang_proses']);
                $table->dropColumn('id_barang_proses');
            }
        });
    }
};
