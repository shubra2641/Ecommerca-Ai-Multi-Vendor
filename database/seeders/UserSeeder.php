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
            ['email' => 'vendor@example.com', 'name' => 'Vendor User', 'role' => 'vendor', 'approved' => true, 'phone' => '0987654321'],
            ['email' => 'user@example.com', 'name' => 'Regular User', 'role' => 'user', 'approved' => false, 'phone' => '1122334455'],
        ];
        foreach ($users as $u) {
            $user = User::updateOrCreate(
                ['email' => $u['email']],
                [
                    'name' => $u['name'],
                    'password' => Hash::make('password'),
                    'role' => $u['role'],
                    'phone_number' => $u['phone'],
                    'whatsapp_number' => $u['phone'],
                    'approved_at' => $u['approved'] ? now() : null,
                ]
            );

            // Ensure email verification timestamp is persisted even though it's not fillable
            if (empty($user->email_verified_at)) {
                $user->forceFill(['email_verified_at' => now()])->save();
            }
        }
    }
}
