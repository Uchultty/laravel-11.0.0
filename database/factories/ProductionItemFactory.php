<?php

namespace Database\Factories;

use App\Models\Material;
use App\Models\Product;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ProductionItem>
 */
class ProductionItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'id_produk' => Product::factory(),
            'id_material' => Material::factory(),
            'id_pelanggan' => Customer::factory(),
            'id_user' => User::factory(),
            'qty' => $this->faker->numberBetween(1, 100),
            'satuan' => 'pcs',
            'ukuran' => $this->faker->word(),
            'tgl_dibuat' => now()->toDateString(),
            'status_kirim' => false,
            'processing' => false,
            'reserve_token' => (string) Str::uuid(),
        ];
    }
}
