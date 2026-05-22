<?php

namespace Database\Seeders;

use App\Models\Supplier;
use App\Models\Customer;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(AuthUserSeeder::class);
        $this->call(SupplierCustomerSeeder::class);
    }
}
