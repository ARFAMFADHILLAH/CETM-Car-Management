<?php

use App\Enums\PeminjamanStatus;
use App\Enums\UserRole;
use App\Models\Car;
use App\Models\Divisi;
use App\Models\Peminjaman;
use App\Models\User;

function adminPersetujuan(): User
{
    return User::factory()->withRole(UserRole::Admin)->create();
}

function peminjam(): User
{
    return User::factory()->withRole(UserRole::User)->create();
}

function pengajuanBaru(User $user): Peminjaman
{
    $car = Car::factory()->create();
    $divisi = Divisi::factory()->create();

    return Peminjaman::factory()->create([
        'nama_peminjam' => $user->nama,
        'email_peminjam' => $user->email,
        'no_hp' => $user->no_hp,
        'divisi_id' => $divisi->id,
        'car_id' => $car->id,
        'status' => PeminjamanStatus::Pending,
    ]);
}

test('admin dapat menyetujui peminjaman dan memberi notifikasi ke peminjam', function () {
    $user = adminPersetujuan();
    $peminjam = peminjam();
    $pengajuan = pengajuanBaru($peminjam);

    $this->actingAs($user)
        ->put(route('approval.approve', $pengajuan->id))
        ->assertRedirect(route('approval.index'));

    expect($pengajuan->refresh()->status)->toBe(PeminjamanStatus::Disetujui);

    $this->assertDatabaseHas('notifikasi', [
        'user_id' => $peminjam->id,
        'tipe' => 'disetujui',
        'dibaca' => false,
    ]);
});

test('admin dapat menolak peminjaman dan memberi notifikasi ke peminjam', function () {
    $user = adminPersetujuan();
    $peminjam = peminjam();
    $pengajuan = pengajuanBaru($peminjam);

    $this->actingAs($user)
        ->put(route('approval.reject', $pengajuan->id))
        ->assertRedirect(route('approval.index'));

    expect($pengajuan->refresh()->status)->toBe(PeminjamanStatus::Ditolak);

    $this->assertDatabaseHas('notifikasi', [
        'user_id' => $peminjam->id,
        'tipe' => 'ditolak',
        'dibaca' => false,
    ]);
});

test('pengguna biasa tidak dapat menyetujui atau menolak peminjaman', function () {
    $user = peminjam();
    $pengajuan = pengajuanBaru($user);

    $this->actingAs($user)
        ->put(route('approval.approve', $pengajuan->id))
        ->assertForbidden();
});
