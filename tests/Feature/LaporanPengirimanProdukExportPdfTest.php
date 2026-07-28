<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Material;
use App\Models\Product;
use App\Models\ProductionItem;
use App\Models\Shipment;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LaporanPengirimanProdukExportPdfTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_export_filtered_laporan_pengiriman_produk_pdf(): void
    {
        $admin = User::factory()->create();
        $customer = Customer::factory()->create(['nama' => 'PT Contoh']);
        $product = Product::query()->create([
            'source_barang_id' => 'BRG-EXPORT-001',
            'kode' => 'PROD-EXPORT-001',
            'nama' => 'Produk Export',
            'satuan' => 'pcs',
            'ukuran' => 'L',
        ]);

        $material = Material::query()->create([
            'source_barang_id' => 'BRG-MAT-001',
            'kode' => 'MAT-EXPORT-001',
            'nama' => 'Material Export',
            'quantity' => 10,
            'stok_minimum' => 0,
            'material_type' => 'Material',
            'satuan' => 'pcs',
            'ukuran' => 'L',
        ]);

        $productionItem = ProductionItem::query()->create([
            'id_produk' => $product->id_product,
            'id_material' => $material->id_material,
            'id_pelanggan' => $customer->id_customer,
            'id_user' => $admin->id_user,
            'qty' => 12,
            'satuan' => 'pcs',
            'ukuran' => 'L',
            'tgl_dibuat' => now()->toDateString(),
            'status_kirim' => true,
            'processing' => false,
        ]);

        Shipment::query()->create([
            'id_produk' => $product->id_product,
            'id_pelanggan' => $customer->id_customer,
            'id_pemesanan_produk' => $productionItem->id_pemesanan_produk,
            'qty' => 12,
            'status_pengiriman' => 'Selesai',
            'tanggal_pengiriman' => now()->toDateString(),
            'id_user' => $admin->id_user,
        ]);

        $response = $this->actingAs($admin)->get(route('laporan-pengiriman-produk.export-pdf', [
            'status' => 'Selesai',
            'search' => 'Produk Export',
        ]));

        $response->assertOk();
        $response->assertHeader('content-type', 'application/pdf');
        $response->assertHeader('content-disposition');
    }
}
