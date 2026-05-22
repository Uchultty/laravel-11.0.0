<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'source_barang_id' => 'BRG' . str_pad((string) $this->faker->unique()->numberBetween(1, 99999), 5, '0', STR_PAD_LEFT),
            'kode' => 'PROD-' . $this->faker->unique()->numerify('######'),
            'nama' => $this->faker->words(3, true),
            'satuan' => 'pcs',
            'ukuran' => $this->faker->word(),
        ];
    }
}
