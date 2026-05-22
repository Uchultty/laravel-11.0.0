<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('barang_dalam_proses', function (Blueprint $table) {
            if (! Schema::hasColumn('barang_dalam_proses', 'status_kirim')) {
                $table->boolean('status_kirim')->default(false)->after('id_barang_keluar');
            }
        });

        if (Schema::hasColumn('barang_dalam_proses', 'tanggal_selesai')) {
            \DB::table('barang_dalam_proses')
                ->whereNotNull('tanggal_selesai')
                ->update(['status_kirim' => true]);
        }
    }

    public function down(): void
    {
        Schema::table('barang_dalam_proses', function (Blueprint $table) {
            if (Schema::hasColumn('barang_dalam_proses', 'status_kirim')) {
                $table->dropColumn('status_kirim');
            }
        });
    }
};
