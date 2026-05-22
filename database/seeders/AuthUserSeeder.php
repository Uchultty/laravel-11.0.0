<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class AuthUserSeeder extends Seeder
{
    /**
     * Seed the admin login account.
     */
    public function run(): void
    {
        User::updateOrCreate([
            'email' => 'admin@gudang.test',
        ], [
            'name' => 'Admin Gudang',
            'password' => 'password',
            'email_verified_at' => now(),
        ]);
    }
}
