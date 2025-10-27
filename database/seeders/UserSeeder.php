<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            ['email' => 'admin@example.com', 'name' => 'Admin User', 'role' => 'admin', 'approved' => true, 'phone' => '1234567890'],
            ['email' => 'vendor@example.com', 'name' => 'Vendor User', 'role' => 'vendor', 'approved' => true, 'phone' => '0987654321'],
            ['email' => 'user@example.com', 'name' => 'Regular User', 'role' => 'user', 'approved' => false, 'phone' => '1122334455'],
        ];
        foreach ($users as $u) {
            User::updateOrCreate(
                ['email' => $u['email']],
                [
                    'name' => $u['name'],
                    'email_verified_at' => now(),
                    'password' => Hash::make('password'),
                    'role' => $u['role'],
                    'phone_number' => $u['phone'],
                    'whatsapp_number' => $u['phone'],
                    'approved_at' => $u['approved'] ? now() : null,
                ]
            );
        }
    }
}
