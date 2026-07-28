<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('barang_keluar', function (Blueprint $table) {
            if (! Schema::hasColumn('barang_keluar', 'id_pemesanan_produk')) {
                $table->unsignedBigInteger('id_pemesanan_produk')->nullable()->after('id_user');
                $table->foreign('id_pemesanan_produk')->references('id_pemesanan_produk')->on('barang_dalam_proses')->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('barang_keluar', function (Blueprint $table) {
            if (Schema::hasColumn('barang_keluar', 'id_pemesanan_produk')) {
                $table->dropForeign(['id_pemesanan_produk']);
                $table->dropColumn('id_pemesanan_produk');
            }
        });
    }
};
