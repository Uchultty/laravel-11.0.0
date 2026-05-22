<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use App\Models\Barang;
use App\Models\BarangMasuk;
use App\Models\BarangDalamProses;
use App\Models\BarangKeluar;
use App\Models\Material;
use App\Models\Product;
use App\Models\MaterialOrder;
use App\Models\ProductionItem;
use App\Models\Shipment;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

Artisan::command('inventory:backfill', function () {
    $this->info('Starting inventory backfill from legacy tables...');

    DB::transaction(function () {
        $barangRows = DB::table('barang')->orderBy('id_barang')->get();

        $materialRows = [];
        $productRows = [];

        foreach ($barangRows as $barang) {
            $payload = [
                'source_barang_id' => $barang->id_barang,
                'kode' => $barang->kode,
                'nama' => $barang->nama,
                'id_jenis_barang' => $barang->id_jenis_barang,
                'quantity' => (int) ($barang->quantity ?? 0),
                'satuan' => $barang->satuan,
                'ukuran' => $barang->ukuran,
                'gambar_path' => $barang->gambar_path,
                'created_at' => $barang->created_at,
                'updated_at' => $barang->updated_at,
            ];

            if (strtolower((string) $barang->status) === 'material') {
                $payload['material_type'] = $barang->material_type;
                $payload['stok_minimum'] = (int) ($barang->stok_minimum ?? 0);
                $materialRows[] = $payload;
                continue;
            }

            $productRows[] = $payload;
        }

        if ($materialRows !== []) {
            DB::table('materials')->upsert(
                $materialRows,
                ['source_barang_id'],
                ['kode', 'nama', 'id_jenis_barang', 'quantity', 'stok_minimum', 'material_type', 'satuan', 'ukuran', 'gambar_path', 'updated_at']
            );
        }

        if ($productRows !== []) {
            DB::table('products')->upsert(
                $productRows,
                ['source_barang_id'],
                ['kode', 'nama', 'id_jenis_barang', 'quantity', 'satuan', 'ukuran', 'gambar_path', 'updated_at']
            );
        }

        $materialMap = DB::table('materials')->pluck('id_material', 'source_barang_id')->all();
        $productMap = DB::table('products')->pluck('id_product', 'source_barang_id')->all();

        $materialOrderRows = [];
        foreach (DB::table('barang_masuk')->orderBy('id_barang_masuk')->get() as $barangMasuk) {
            $materialId = $materialMap[(string) $barangMasuk->id_barang] ?? null;
            if (! $materialId) {
                continue;
            }

            $materialOrderRows[] = [
                'legacy_barang_masuk_id' => $barangMasuk->id_barang_masuk,
                'id_material' => $materialId,
                'id_supplier' => $barangMasuk->id_supplier,
                'id_user' => $barangMasuk->id_user,
                'qty' => (int) $barangMasuk->quantity,
                'satuan' => $barangMasuk->satuan,
                'status' => $barangMasuk->status,
                'tgl_pemesanan' => $barangMasuk->tanggal_masuk,
                'estimasi_tiba' => $barangMasuk->estimasi_tiba,
                'invoice_path' => $barangMasuk->invoice_path,
                'surat_jalan_path' => $barangMasuk->surat_jalan_path,
                'gambar_path' => $barangMasuk->gambar_path,
                'created_at' => $barangMasuk->created_at,
                'updated_at' => $barangMasuk->updated_at,
            ];
        }

        if ($materialOrderRows !== []) {
                DB::table('pemesanan_material')->upsert(
                $materialOrderRows,
                ['legacy_barang_masuk_id'],
                ['id_material', 'id_supplier', 'id_user', 'qty', 'satuan', 'status', 'tgl_pemesanan', 'estimasi_tiba', 'invoice_path', 'surat_jalan_path', 'gambar_path', 'updated_at']
            );
        }

        $productionRows = [];
        foreach (DB::table('barang_dalam_proses')->orderBy('id_barang_proses')->get() as $barangDalamProses) {
            $productId = $productMap[(string) $barangDalamProses->id_barang] ?? null;
            $materialId = $materialMap[(string) $barangDalamProses->id_barang_mentah] ?? null;

            if (! $productId || ! $materialId) {
                continue;
            }

            $productionRows[] = [
                'legacy_barang_proses_id' => (string) $barangDalamProses->id_barang_proses,
                'id_produk' => $productId,
                'id_material' => $materialId,
                'id_pelanggan' => $barangDalamProses->id_customer,
                'id_user' => $barangDalamProses->id_user,
                'qty' => (int) $barangDalamProses->quantity,
                'satuan' => $barangDalamProses->satuan,
                'ukuran' => $barangDalamProses->ukuran,
                'tgl_dibuat' => optional($barangDalamProses->created_at)->toDateString() ?? now()->toDateString(),
                'tgl_selesai' => $barangDalamProses->tanggal_selesai,
                'status_kirim' => (bool) $barangDalamProses->status_kirim,
                'processing' => (bool) $barangDalamProses->processing,
                'reserve_token' => $barangDalamProses->reserve_token,
                'processing_started_at' => $barangDalamProses->processing_started_at,
                'processing_by' => $barangDalamProses->processing_by,
                'created_at' => $barangDalamProses->created_at,
                'updated_at' => $barangDalamProses->updated_at,
            ];
        }

        if ($productionRows !== []) {
            DB::table('barang_dalam_proses')->upsert(
                $productionRows,
                ['legacy_barang_proses_id'],
                ['id_produk', 'id_material', 'id_pelanggan', 'id_user', 'qty', 'satuan', 'ukuran', 'tgl_dibuat', 'tgl_selesai', 'status_kirim', 'processing', 'reserve_token', 'processing_started_at', 'processing_by', 'updated_at']
            );
        }

        $productionMap = DB::table('barang_dalam_proses')->pluck('id_barang_proses', 'legacy_barang_proses_id')->all();

        $shipmentRows = [];
        foreach (DB::table('barang_keluar')->orderBy('id_barang_keluar')->get() as $barangKeluar) {
            $productId = $productMap[(string) $barangKeluar->id_barang] ?? null;
            $productionId = $productionMap[(string) $barangKeluar->id_barang_proses] ?? null;

            if (! $productId || ! $productionId) {
                continue;
            }

            $shipmentRows[] = [
                'legacy_barang_keluar_id' => $barangKeluar->id_barang_keluar,
                'id_barang_proses' => $productionId,
                'id_produk' => $productId,
                'id_pelanggan' => $barangKeluar->id_customer,
                'id_user' => $barangKeluar->id_user,
                'qty' => (int) $barangKeluar->quantity,
                'tanggal_pengiriman' => $barangKeluar->tanggal_keluar,
                'status_pengiriman' => $barangKeluar->status_pengiriman,
                'material_type' => $barangKeluar->material_type,
                'invoice_path' => $barangKeluar->invoice_path,
                'surat_jalan_path' => $barangKeluar->surat_jalan_path,
                'gambar_path' => $barangKeluar->gambar_path,
                'created_at' => $barangKeluar->created_at,
                'updated_at' => $barangKeluar->updated_at,
            ];
        }

        if ($shipmentRows !== []) {
            DB::table('pengiriman_barang')->upsert(
                $shipmentRows,
                ['legacy_barang_keluar_id'],
                ['id_barang_proses', 'id_produk', 'id_pelanggan', 'id_user', 'qty', 'tanggal_pengiriman', 'status_pengiriman', 'material_type', 'invoice_path', 'surat_jalan_path', 'gambar_path', 'updated_at']
            );
        }
    });

    $this->info('Backfill completed successfully.');
})->purpose('Backfill modular inventory tables from legacy schema');

Artisan::command('inventory:validate-parity', function () {
    $this->info('Validating inventory parity between legacy and modular tables...');

    $issues = [];

    $checks = [
        [
            'label' => 'materials',
            'legacyCount' => DB::table('barang')->where('status', 'material')->count(),
            'modularCount' => DB::table('materials')->count(),
        ],
        [
            'label' => 'products',
            'legacyCount' => DB::table('barang')->where('status', '!=', 'material')->count(),
            'modularCount' => DB::table('products')->count(),
        ],
        [
            'label' => 'material_orders',
            'legacyCount' => DB::table('barang_masuk')->count(),
            'modularCount' => DB::table('material_orders')->count(),
        ],
        [
            'label' => 'barang_dalam_proses',
            'legacyCount' => DB::table('barang_dalam_proses')->count(),
            'modularCount' => DB::table('barang_dalam_proses')->count(),
        ],
        [
            'label' => 'pengiriman_barang',
            'legacyCount' => DB::table('barang_keluar')->count(),
            'modularCount' => DB::table('pengiriman_barang')->count(),
        ],
    ];

    foreach ($checks as $check) {
        if ($check['legacyCount'] !== $check['modularCount']) {
            $issues[] = sprintf(
                '%s count mismatch: legacy=%d modular=%d',
                $check['label'],
                $check['legacyCount'],
                $check['modularCount']
            );
        } else {
            $this->line(sprintf('%s count OK: %d', $check['label'], $check['legacyCount']));
        }
    }

    $orphanChecks = [
        [
            'label' => 'material_orders',
            'query' => DB::table('material_orders as mo')
                ->leftJoin('materials as m', 'm.id_material', '=', 'mo.id_material')
                ->whereNull('m.id_material')
                ->count(),
        ],
        [
            'label' => 'barang_dalam_proses.material',
            'query' => DB::table('barang_dalam_proses as pi')
                ->leftJoin('materials as m', 'm.id_material', '=', 'pi.id_material')
                ->whereNull('m.id_material')
                ->count(),
        ],
        [
            'label' => 'barang_dalam_proses.product',
            'query' => DB::table('barang_dalam_proses as pi')
                ->leftJoin('products as p', 'p.id_product', '=', 'pi.id_produk')
                ->whereNull('p.id_product')
                ->count(),
        ],
        [
            'label' => 'pengiriman_barang.production_item',
            'query' => DB::table('pengiriman_barang as s')
                ->leftJoin('barang_dalam_proses as pi', 'pi.id_barang_proses', '=', 's.id_barang_proses')
                ->whereNull('pi.id_barang_proses')
                ->count(),
        ],
    ];

    foreach ($orphanChecks as $orphanCheck) {
        if ($orphanCheck['query'] > 0) {
            $issues[] = sprintf('%s orphan rows: %d', $orphanCheck['label'], $orphanCheck['query']);
        } else {
            $this->line(sprintf('%s orphan check OK', $orphanCheck['label']));
        }
    }

    if ($issues !== []) {
        $this->error('Parity validation failed:');

        foreach ($issues as $issue) {
            $this->error('- ' . $issue);
        }

        return 1;
    }

    $this->info('Parity validation passed.');

    return 0;
})->purpose('Validate parity between legacy and modular inventory tables');
