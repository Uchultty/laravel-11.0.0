<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('shipments') && ! Schema::hasTable('pengiriman_barang')) {
            Schema::rename('shipments', 'pengiriman_barang');
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('pengiriman_barang') && ! Schema::hasTable('shipments')) {
            Schema::rename('pengiriman_barang', 'shipments');
        }
    }
};
