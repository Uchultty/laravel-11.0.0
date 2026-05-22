<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Product;
use App\Models\ProductionItem;
use App\Models\Shipment;
use App\Models\Customer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ShipmentFlowTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected Customer $customer;
    protected Product $product;
    protected ProductionItem $productionItem;

    protected function setUp(): void
    {
        parent::setUp();

        // Create users
        $this->admin = User::factory()->create();

        // Create test data
        $this->customer = Customer::factory()->create();
        $this->product = Product::factory()->create();

        // Create production item (barang dalam proses)
        $this->productionItem = ProductionItem::factory()->create([
            'id_produk' => $this->product->id_product,
            'id_pelanggan' => $this->customer->id_customer,
            'status_kirim' => true,
        ]);
    }

    public function test_admin_can_create_shipment()
    {
        // Create a production item first
        $productionItem = ProductionItem::factory()->create([
            'id_produk' => $this->product->id_product,
            'id_pelanggan' => $this->customer->id_customer,
            'status_kirim' => true,
        ]);

        $this->actingAs($this->admin)
            ->post(route('pengiriman-produk.store'), [
                'id_barang' => $this->product->id_product,
                'id_customer' => $this->customer->id_customer,
                'quantity' => 5,
                'tanggal_keluar' => now()->toDateString(),
                'status_pengiriman' => 'Siap Dikirim',
                'id_barang_proses' => $productionItem->id_barang_proses,
            ])
            ->assertRedirect(route('pengiriman-produk.index'));

        $this->assertDatabaseHas('pengiriman_barang', [
            'id_produk' => $this->product->id_product,
            'id_pelanggan' => $this->customer->id_customer,
            'qty' => 5,
        ]);
    }

    public function test_admin_can_view_shipment()
    {
        $shipment = Shipment::factory()->create([
            'id_produk' => $this->product->id_product,
            'id_pelanggan' => $this->customer->id_customer,
        ]);

        $this->actingAs($this->admin)
            ->get(route('pengiriman-produk.show', $shipment))
            ->assertOk()
            ->assertViewHas('pengiriman_produk');
    }

    public function test_admin_can_edit_shipment()
    {
        $shipment = Shipment::factory()->create([
            'id_produk' => $this->product->id_product,
            'id_pelanggan' => $this->customer->id_customer,
        ]);

        $this->actingAs($this->admin)
            ->get(route('pengiriman-produk.edit', $shipment))
            ->assertOk()
            ->assertViewHas('pengiriman_produk');
    }

    public function test_admin_can_update_shipment()
    {
        $shipment = Shipment::factory()->create([
            'id_produk' => $this->product->id_product,
            'id_pelanggan' => $this->customer->id_customer,
            'qty' => 5,
        ]);

        $this->actingAs($this->admin)
            ->put(route('pengiriman-produk.update', $shipment), [
                'id_barang' => $this->product->id_product,
                'id_customer' => $this->customer->id_customer,
                'quantity' => 10,
                'tanggal_keluar' => now()->toDateString(),
                'status_pengiriman' => 'Sedang Dikirim',
            ])
            ->assertRedirect(route('pengiriman-produk.index'));

        $this->assertDatabaseHas('pengiriman_barang', [
            'id_pengiriman' => $shipment->id_pengiriman,
            'qty' => 10,
            'status_pengiriman' => 'Sedang Dikirim',
        ]);
    }

    public function test_admin_can_delete_shipment()
    {
        $shipment = Shipment::factory()->create([
            'id_produk' => $this->product->id_product,
            'id_pelanggan' => $this->customer->id_customer,
        ]);

        $this->actingAs($this->admin)
            ->delete(route('pengiriman-produk.destroy', $shipment))
            ->assertRedirect(route('pengiriman-produk.index'));

        $this->assertDatabaseMissing('pengiriman_barang', [
            'id_pengiriman' => $shipment->id_pengiriman,
        ]);
    }
}
