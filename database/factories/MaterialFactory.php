<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Material>
 */
class MaterialFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'source_barang_id' => 'BRG' . str_pad((string) $this->faker->unique()->numberBetween(100000, 999999), 5, '0', STR_PAD_LEFT),
            'kode' => 'MAT-' . $this->faker->unique()->numerify('######'),
            'nama' => $this->faker->words(2, true),
            'quantity' => $this->faker->numberBetween(0, 5000),
            'material_type' => $this->faker->word(),
            'stok_minimum' => 10,
            'satuan' => 'kg',
            'ukuran' => $this->faker->word(),
        ];
    }
}
