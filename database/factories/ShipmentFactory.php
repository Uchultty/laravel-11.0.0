<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\ProductionItem;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Shipment>
 */
class ShipmentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'id_pemesanan_produk' => ProductionItem::factory(),
            'id_produk' => Product::factory(),
            'id_pelanggan' => Customer::factory(),
            'id_user' => User::factory(),
            'qty' => $this->faker->numberBetween(1, 100),
            'tanggal_pengiriman' => now()->toDateString(),
            'status_pengiriman' => 'Siap Dikirim',
        ];
    }
}
