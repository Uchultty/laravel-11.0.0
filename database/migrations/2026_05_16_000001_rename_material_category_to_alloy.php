<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Rename data lama supaya istilah di database mengikuti struktur aplikasi sekarang.
        DB::table('jenis_barang')
            ->where('nama', 'ALLOY')
            ->update(['nama' => 'Material']);
    }

    public function down(): void
    {
        // Rollback ke nama lama kalau migrasi ini dibatalkan.
        DB::table('jenis_barang')
            ->where('nama', 'Material')
            ->update(['nama' => 'ALLOY']);
    }
};
