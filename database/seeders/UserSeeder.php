<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\Role;
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
        $adminRole = Role::query()->where('role', UserRole::Admin->value)->firstOrFail();
        $userRole = Role::query()->where('role', UserRole::User->value)->firstOrFail();

        User::query()->firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'nama' => 'Administrator',
                'password' => Hash::make('password'),
                'no_hp' => '081234567890',
                'email_verified_at' => now(),
                'role_id' => $adminRole->id,
            ],
        );

        $users = [
            ['nama' => 'Budi Santoso', 'email' => 'budi@example.com', 'no_hp' => '081200000001'],
            ['nama' => 'Siti Rahayu', 'email' => 'siti@example.com', 'no_hp' => '081200000002'],
            ['nama' => 'Andi Wijaya', 'email' => 'andi@example.com', 'no_hp' => '081200000003'],
        ];

        foreach ($users as $user) {
            User::query()->firstOrCreate(
                ['email' => $user['email']],
                [
                    ...$user,
                    'password' => Hash::make('password'),
                    'email_verified_at' => now(),
                    'role_id' => $userRole->id,
                ],
            );
        }
    }
}
