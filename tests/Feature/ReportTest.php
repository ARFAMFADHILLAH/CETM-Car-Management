<?php

use App\Enums\UserRole;
use App\Models\Car;
use App\Models\Divisi;
use App\Models\Peminjaman;
use App\Models\User;
use Carbon\Carbon;
use Inertia\Testing\AssertableInertia as Assert;

test('admin dapat mengakses halaman laporan', function () {
    $user = User::factory()->withRole(UserRole::Admin)->create();

    $this->actingAs($user)
        ->get(route('laporan.index'))
        ->assertOk()
        ->assertInertia(
            fn (Assert $page) => $page->component('admin/laporan/Index'),
        );
});

test('pengguna tidak dapat mengakses halaman laporan', function () {
    $user = User::factory()->withRole(UserRole::User)->create();

    $this->actingAs($user)
        ->get(route('laporan.index'))
        ->assertForbidden();
});

test('admin dapat melihat laporan peminjaman', function () {
    $user = User::factory()->withRole(UserRole::Admin)->create();
    $car = Car::factory()->create();
    $divisi = Divisi::factory()->create();

    Peminjaman::factory()->create([
        'car_id' => $car->id,
        'divisi_id' => $divisi->id,
        'tanggal_mulai' => Carbon::now()->addDay(),
        'tanggal_selesai' => Carbon::now()->addDays(2),
    ]);

    $this->actingAs($user)
        ->get(route('laporan.peminjaman'))
        ->assertOk()
        ->assertInertia(
            fn (Assert $page) => $page
                ->component('admin/laporan/Index')
                ->has('laporanPeminjaman')
                ->has('divisiList'),
        );
});

test('admin dapat melihat laporan mobil', function () {
    $user = User::factory()->withRole(UserRole::Admin)->create();
    Car::factory()->count(3)->create();

    $this->actingAs($user)
        ->get(route('laporan.mobil'))
        ->assertOk()
        ->assertInertia(
            fn (Assert $page) => $page
                ->component('admin/laporan/Index')
                ->has('laporanMobil'),
        );
});

test('admin dapat melihat laporan pengguna', function () {
    $user = User::factory()->withRole(UserRole::Admin)->create();
    User::factory()->count(2)->create();

    $this->actingAs($user)
        ->get(route('laporan.pengguna'))
        ->assertOk()
        ->assertInertia(
            fn (Assert $page) => $page
                ->component('admin/laporan/Index')
                ->has('laporanPengguna'),
        );
});

test('admin dapat export pdf peminjaman', function () {
    $user = User::factory()->withRole(UserRole::Admin)->create();

    $this->actingAs($user)
        ->get(route('laporan.export.pdf', 'peminjaman'))
        ->assertOk()
        ->assertHeader('content-type', 'application/pdf');
});

test('admin dapat export excel peminjaman', function () {
    $user = User::factory()->withRole(UserRole::Admin)->create();

    $this->actingAs($user)
        ->get(route('laporan.export.excel', 'peminjaman'))
        ->assertOk();
});

test('filter peminjaman by status berfungsi', function () {
    $user = User::factory()->withRole(UserRole::Admin)->create();
    $car = Car::factory()->create();
    $divisi = Divisi::factory()->create();

    Peminjaman::factory()->create([
        'car_id' => $car->id,
        'divisi_id' => $divisi->id,
        'status' => 'disetujui',
        'tanggal_mulai' => Carbon::now()->addDay(),
        'tanggal_selesai' => Carbon::now()->addDays(2),
    ]);

    Peminjaman::factory()->create([
        'car_id' => $car->id,
        'divisi_id' => $divisi->id,
        'status' => 'pending',
        'tanggal_mulai' => Carbon::now()->addDay(),
        'tanggal_selesai' => Carbon::now()->addDays(2),
    ]);

    $this->actingAs($user)
        ->get(route('laporan.peminjaman', ['status' => 'disetujui']))
        ->assertOk()
        ->assertInertia(
            fn (Assert $page) => $page
                ->component('admin/laporan/Index')
                ->has('laporanPeminjaman', 1),
        );
});
