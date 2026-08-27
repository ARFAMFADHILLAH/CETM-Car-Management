<?php

use App\Enums\NotifikasiTipe;
use App\Enums\UserRole;
use App\Models\Notifikasi;
use App\Models\User;

function penggunaNotifikasi(): User
{
    return User::factory()->withRole(UserRole::User)->create();
}

test('halaman notifikasi menampilkan hanya notifikasi milik pengguna', function () {
    $user = penggunaNotifikasi();
    $lainnya = penggunaNotifikasi();

    Notifikasi::factory()->count(2)->create(['user_id' => $user->id]);
    Notifikasi::factory()->create(['user_id' => $lainnya->id]);

    $this->actingAs($user)
        ->get(route('notifikasi.index'))
        ->assertOk()
        ->assertInertia(
            fn ($page) => $page
                ->component('notifikasi/Index')
                ->has('notifikasi', 2),
        );
});

test('pengguna dapat menandai satu notifikasi sudah dibaca', function () {
    $user = penggunaNotifikasi();
    $notifikasi = Notifikasi::factory()->belumDibaca()->create(['user_id' => $user->id]);

    $this->actingAs($user)
        ->put(route('notifikasi.read', $notifikasi->id))
        ->assertRedirect();

    expect($notifikasi->refresh()->dibaca)->toBeTrue();
});

test('pengguna tidak dapat menandai notifikasi milik orang lain', function () {
    $user = penggunaNotifikasi();
    $notifikasi = Notifikasi::factory()->create(['user_id' => penggunaNotifikasi()->id]);

    $this->actingAs($user)
        ->put(route('notifikasi.read', $notifikasi->id))
        ->assertForbidden();
});

test('pengguna dapat menandai semua notifikasi sudah dibaca', function () {
    $user = penggunaNotifikasi();

    Notifikasi::factory()->count(3)->belumDibaca()->create(['user_id' => $user->id]);

    $this->actingAs($user)
        ->put(route('notifikasi.readAll'))
        ->assertRedirect();

    expect(Notifikasi::where('user_id', $user->id)->where('dibaca', false)->count())->toBe(0);
});

test('shared props mengirim jumlah notifikasi belum dibaca', function () {
    $user = penggunaNotifikasi();

    Notifikasi::factory()->count(2)->belumDibaca()->create([
        'user_id' => $user->id,
        'tipe' => NotifikasiTipe::Pengingat,
    ]);

    $this->actingAs($user)
        ->get(route('dashboard'))
        ->assertInertia(
            fn ($page) => $page
                ->component('Dashboard')
                ->where('notifikasiBelumDibaca', 2),
        );
});
