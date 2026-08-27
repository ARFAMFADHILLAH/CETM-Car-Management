<?php

use App\Enums\UserRole;
use App\Models\Peminjaman;
use App\Models\User;

test('halaman jadwal menampilkan peminjaman disetujui dan selesai', function () {
    $user = User::factory()->withRole(UserRole::Admin)->create();

    Peminjaman::factory()->disetujui()->create();
    Peminjaman::factory()->selesai()->create();
    Peminjaman::factory()->create();
    Peminjaman::factory()->ditolak()->create();

    $this->actingAs($user)
        ->get(route('jadwal.index'))
        ->assertOk()
        ->assertInertia(
            fn ($page) => $page
                ->component('jadwal/Index')
                ->has('peminjaman', 2),
        );
});
