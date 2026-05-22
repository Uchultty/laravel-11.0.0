<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('materials')->insertUsing(
            [
                'source_barang_id',
                'kode',
                'nama',
                'id_jenis_barang',
                'quantity',
                'stok_minimum',
                'material_type',
                'satuan',
                'ukuran',
                'gambar_path',
                'created_at',
                'updated_at',
            ],
            DB::table('barang as b')
                ->selectRaw('b.id_barang as source_barang_id, b.kode, b.nama, b.id_jenis_barang, b.quantity, b.stok_minimum, b.material_type, b.satuan, b.ukuran, b.gambar_path, b.created_at, b.updated_at')
                ->whereRaw("LOWER(COALESCE(b.status, '')) = 'material'")
                ->whereNotExists(function ($query) {
                    $query->selectRaw('1')
                        ->from('materials as m')
                        ->whereRaw('m.source_barang_id = b.id_barang');
                })
        );

        DB::table('products')->insertUsing(
            [
                'source_barang_id',
                'kode',
                'nama',
                'id_jenis_barang',
                'quantity',
                'panjang',
                'lebar',
                'tinggi',
                'satuan',
                'ukuran',
                'gambar_path',
                'created_at',
                'updated_at',
            ],
            DB::table('barang as b')
                ->selectRaw('b.id_barang as source_barang_id, b.kode, b.nama, b.id_jenis_barang, b.quantity, b.panjang, b.lebar, b.tinggi, b.satuan, b.ukuran, b.gambar_path, b.created_at, b.updated_at')
                ->whereRaw("LOWER(COALESCE(b.status, 'produk')) <> 'material'")
                ->whereNotExists(function ($query) {
                    $query->selectRaw('1')
                        ->from('products as p')
                        ->whereRaw('p.source_barang_id = b.id_barang');
                })
        );

        DB::table('material_orders')->insertUsing(
            [
                'legacy_barang_masuk_id',
                'id_material',
                'id_supplier',
                'id_user',
                'qty',
                'satuan',
                'status',
                'tgl_pemesanan',
                'estimasi_tiba',
                'invoice_path',
                'surat_jalan_path',
                'gambar_path',
                'created_at',
                'updated_at',
            ],
            DB::table('barang_masuk as bm')
                ->join('materials as m', 'm.source_barang_id', '=', 'bm.id_barang')
                ->selectRaw('CAST(bm.id_barang_masuk AS text) as legacy_barang_masuk_id, m.id_material as id_material, bm.id_supplier, bm.id_user, bm.quantity as qty, bm.satuan, bm.status, bm.tanggal_masuk as tgl_pemesanan, bm.estimasi_tiba, bm.invoice_path, bm.surat_jalan_path, bm.gambar_path, bm.created_at, bm.updated_at')
                ->whereNotExists(function ($query) {
                    $query->selectRaw('1')
                        ->from('material_orders as mo')
                        ->whereRaw('mo.legacy_barang_masuk_id = CAST(bm.id_barang_masuk AS text)');
                })
        );

        DB::table('production_items')->insertUsing(
            [
                'legacy_barang_proses_id',
                'id_produk',
                'id_material',
                'id_pelanggan',
                'id_user',
                'qty',
                'satuan',
                'ukuran',
                'tgl_dibuat',
                'tgl_selesai',
                'status_kirim',
                'processing',
                'reserve_token',
                'processing_started_at',
                'processing_by',
                'created_at',
                'updated_at',
            ],
            DB::table('barang_dalam_proses as bdp')
                ->join('products as p', 'p.source_barang_id', '=', 'bdp.id_barang')
                ->join('materials as m', 'm.source_barang_id', '=', 'bdp.id_barang_mentah')
                ->selectRaw('CAST(bdp.id_barang_proses AS text) as legacy_barang_proses_id, p.id_product as id_produk, m.id_material as id_material, bdp.id_customer as id_pelanggan, bdp.id_user, bdp.quantity as qty, bdp.satuan, bdp.ukuran, DATE(bdp.created_at) as tgl_dibuat, bdp.tanggal_selesai as tgl_selesai, bdp.status_kirim, bdp.processing, bdp.reserve_token, bdp.processing_started_at, bdp.processing_by, bdp.created_at, bdp.updated_at')
                ->whereNotExists(function ($query) {
                    $query->selectRaw('1')
                        ->from('production_items as pi')
                        ->whereRaw('pi.legacy_barang_proses_id = CAST(bdp.id_barang_proses AS text)');
                })
        );

        DB::table('shipments')->insertUsing(
            [
                'legacy_barang_keluar_id',
                'id_barang_proses',
                'id_produk',
                'id_pelanggan',
                'id_user',
                'qty',
                'tanggal_pengiriman',
                'status_pengiriman',
                'material_type',
                'invoice_path',
                'surat_jalan_path',
                'gambar_path',
                'created_at',
                'updated_at',
            ],
            DB::table('barang_keluar as bk')
                ->join('products as p', 'p.source_barang_id', '=', 'bk.id_barang')
                ->selectRaw('CAST(bk.id_barang_keluar AS text) as legacy_barang_keluar_id, bk.id_barang_proses, p.id_product as id_produk, bk.id_customer as id_pelanggan, bk.id_user, bk.quantity as qty, bk.tanggal_keluar as tanggal_pengiriman, bk.status_pengiriman, bk.material_type, bk.invoice_path, bk.surat_jalan_path, bk.gambar_path, bk.created_at, bk.updated_at')
                ->whereNotExists(function ($query) {
                    $query->selectRaw('1')
                        ->from('shipments as s')
                        ->whereRaw('s.legacy_barang_keluar_id = CAST(bk.id_barang_keluar AS text)');
                })
        );
    }

    public function down(): void
    {
        DB::table('shipments')
            ->whereNotNull('legacy_barang_keluar_id')
            ->delete();

        DB::table('production_items')
            ->whereNotNull('legacy_barang_proses_id')
            ->delete();

        DB::table('pemesanan_material')
            ->whereNotNull('legacy_barang_masuk_id')
            ->delete();

        DB::table('products')
            ->whereNotNull('source_barang_id')
            ->delete();

        DB::table('materials')
            ->whereNotNull('source_barang_id')
            ->delete();
    }
};
