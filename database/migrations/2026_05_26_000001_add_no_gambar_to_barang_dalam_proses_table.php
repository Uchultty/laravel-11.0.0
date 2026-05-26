<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('barang_dalam_proses', function (Blueprint $table): void {
            if (! Schema::hasColumn('barang_dalam_proses', 'no_gambar')) {
                $table->string('no_gambar', 100)->nullable()->after('id_produk');
            }
        });
    }

    public function down(): void
    {
        Schema::table('barang_dalam_proses', function (Blueprint $table): void {
            if (Schema::hasColumn('barang_dalam_proses', 'no_gambar')) {
                $table->dropColumn('no_gambar');
            }
        });
    }
};
