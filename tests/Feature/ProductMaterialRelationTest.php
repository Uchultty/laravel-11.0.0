<?php

namespace Tests\Feature;

use App\Models\Material;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductMaterialRelationTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_store_product_with_materials(): void
    {
        $admin = User::factory()->create();
        $materialA = Material::factory()->create(['nama' => 'ALUMINIUM']);
        $materialB = Material::factory()->create(['nama' => 'BAJA']);

        $this->actingAs($admin)
            ->post(route('data-produk.store'), [
                'form_mode' => 'produk',
                'nama' => 'Produk Komposisi',
                'satuan' => 'mm',
                'ukuran' => '10x20 mm',
                'materials' => [
                    ['id_material' => $materialA->id_material],
                    ['id_material' => $materialB->id_material],
                ],
            ])
            ->assertRedirect(route('data-produk.index'));

        $product = Product::query()->where('nama', 'PRODUK KOMPOSISI')->firstOrFail();

        $this->assertDatabaseHas('product_material', [
            'id_product' => $product->id_product,
            'id_material' => $materialA->id_material,
        ]);

        $this->assertDatabaseHas('product_material', [
            'id_product' => $product->id_product,
            'id_material' => $materialB->id_material,
        ]);

        $product->load('materials');

        $this->assertCount(2, $product->materials);
    }

    public function test_admin_can_update_product_materials(): void
    {
        $admin = User::factory()->create();
        $materialA = Material::factory()->create(['nama' => 'ALUMINIUM']);
        $materialB = Material::factory()->create(['nama' => 'BAJA']);
        $materialC = Material::factory()->create(['nama' => 'TEMBAGA']);

        $product = Product::factory()->create([
            'nama' => 'PRODUK KOMPOSISI',
            'satuan' => 'mm',
            'ukuran' => '10x20 mm',
        ]);

        $product->materials()->sync([
            $materialA->id_material => ['quantity' => 1],
            $materialB->id_material => ['quantity' => 2],
        ]);

        $this->actingAs($admin)
            ->put(route('data-produk.update', $product), [
                'form_mode' => 'produk',
                'nama' => 'Produk Komposisi Revisi',
                'satuan' => 'mm',
                'ukuran' => '12x24 mm',
                'materials' => [
                    ['id_material' => $materialB->id_material],
                    ['id_material' => $materialC->id_material],
                ],
            ])
            ->assertRedirect(route('data-produk.index'));

        $product->refresh()->load('materials');

        $this->assertSame('PRODUK KOMPOSISI REVISI', $product->nama);
        $this->assertCount(2, $product->materials);
        $this->assertTrue($product->materials->contains('id_material', $materialB->id_material));
        $this->assertTrue($product->materials->contains('id_material', $materialC->id_material));
        $this->assertFalse($product->materials->contains('id_material', $materialA->id_material));
    }
}
