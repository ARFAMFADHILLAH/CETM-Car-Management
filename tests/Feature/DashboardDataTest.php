<?php

use App\Enums\PeminjamanStatus;
use App\Enums\UserRole;
use App\Models\Car;
use App\Models\Peminjaman;
use App\Models\User;

function userDenganRole(UserRole $role): User
{
    return User::factory()->withRole($role)->create();
}

test('dashboard admin menampilkan statistik dan pengajuan pending', function () {
    $user = userDenganRole(UserRole::Admin);

    Car::factory()->count(2)->create();
    Car::factory()->tidakTersedia()->create();

    $peminjam = userDenganRole(UserRole::User);
    Peminjaman::factory()->create([
        'email_peminjam' => $peminjam->email,
        'status' => PeminjamanStatus::Pending,
    ]);

    $this->actingAs($user)
        ->get(route('dashboard'))
        ->assertOk()
        ->assertInertia(
            fn ($page) => $page
                ->component('Dashboard')
                ->where('isAdmin', true)
                ->where('totalMobil', 4)
                ->where('mobilTersedia', 3)
                ->has('pengajuanPending', 1),
        );
});
