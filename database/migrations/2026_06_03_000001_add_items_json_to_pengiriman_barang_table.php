<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('pengiriman_barang', 'items')) {
            Schema::table('pengiriman_barang', function (Blueprint $table) {
                $table->json('items')->nullable()->after('no_gambar');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('pengiriman_barang', 'items')) {
            Schema::table('pengiriman_barang', function (Blueprint $table) {
                $table->dropColumn('items');
            });
        }
    }
};
