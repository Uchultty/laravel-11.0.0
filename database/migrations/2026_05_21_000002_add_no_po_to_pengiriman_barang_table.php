<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('pengiriman_barang', 'no_po')) {
            Schema::table('pengiriman_barang', function (Blueprint $table) {
                $table->string('no_po', 100)->nullable()->after('id_pemesanan_produk');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('pengiriman_barang', 'no_po')) {
            Schema::table('pengiriman_barang', function (Blueprint $table) {
                $table->dropColumn('no_po');
            });
        }
    }
};
