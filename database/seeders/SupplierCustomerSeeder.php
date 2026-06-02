<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Supplier;
use Illuminate\Database\Seeder;

class SupplierCustomerSeeder extends Seeder
{
    /**
     * Seed dummy suppliers and customers.
     */
    public function run(): void
    {
        Supplier::factory()->count(5)->create();
        Customer::factory()->count(5)->create();
    }
}
