<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $latestJenisPerBarang = DB::table('barang_masuk as bm')
            ->select('bm.barang_id', 'bm.jenis_barang_id')
            ->whereNotNull('bm.jenis_barang_id')
            ->whereRaw('bm.id_barang_masuk = (
                SELECT bm2.id_barang_masuk
                FROM barang_masuk bm2
                WHERE bm2.barang_id = bm.barang_id
                  AND bm2.jenis_barang_id IS NOT NULL
                ORDER BY bm2.tanggal_masuk DESC, bm2.created_at DESC
                LIMIT 1
            )')
            ->get();

        foreach ($latestJenisPerBarang as $row) {
            DB::table('barang')
                ->where('id_barang', $row->barang_id)
                ->update(['jenis_barang_id' => $row->jenis_barang_id]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No-op: backfill data migration.
    }
};
