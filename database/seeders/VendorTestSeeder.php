<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class VendorTestSeeder extends Seeder
{
    public function run()
    {
        // Create a simple vendor user for local testing
        User::updateOrCreate([
            'email' => 'vendor@example.test',
        ], [
            'name' => 'Test Vendor',
            'password' => Hash::make('password123'),
            'role' => 'vendor',
            'email_verified_at' => now(),
        ]);
    }
}
