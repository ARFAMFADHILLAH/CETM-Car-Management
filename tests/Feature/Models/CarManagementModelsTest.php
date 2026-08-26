<?php

use App\Enums\CarStatus;
use App\Enums\PeminjamanStatus;
use App\Enums\UserRole;
use App\Models\Car;
use App\Models\Divisi;
use App\Models\Peminjaman;
use App\Models\Role;
use App\Models\User;
use Carbon\CarbonInterface;
use Illuminate\Database\QueryException;

test('user factory creates user with nama and no_hp', function () {
    $user = User::factory()->create([
        'nama' => 'Budi Santoso',
        'no_hp' => '081234567890',
    ]);

    $this->assertDatabaseHas('users', [
        'id' => $user->id,
        'nama' => 'Budi Santoso',
        'no_hp' => '081234567890',
    ]);
});

test('user can have an admin role', function () {
    $admin = User::factory()->admin()->create();

    expect($admin->role->role)->toBe(UserRole::Admin);
    expect($admin->isAdmin())->toBeTrue();
});

test('user without role is not admin', function () {
    $user = User::factory()->create();

    expect($user->role)->toBeNull();
    expect($user->isAdmin())->toBeFalse();
});

test('role seeder creates admin and user roles', function () {
    $this->seed(RoleSeeder::class);

    expect(Role::query()->count())->toBe(2);
    expect(Role::query()->where('role', UserRole::Admin->value)->exists())->toBeTrue();
    expect(Role::query()->where('role', UserRole::User->value)->exists())->toBeTrue();
});

test('car factory creates cars with unique plates', function () {
    $cars = Car::factory()->count(3)->create();

    expect($cars->pluck('nomor_plat')->unique())->toHaveCount(3)
        ->and($cars->first()->nama)->not->toBeEmpty();
});

test('car has tersedia status by default', function () {
    $car = Car::factory()->create();

    expect($car->refresh()->status)->toBe(CarStatus::Tersedia);
});

test('car status states are applied correctly', function () {
    expect(Car::factory()->tidakTersedia()->create()->refresh()->status)->toBe(CarStatus::TidakTersedia)
        ->and(Car::factory()->diServis()->create()->refresh()->status)->toBe(CarStatus::DiServis);
});

test('peminjaman belongs to car and divisi with pending status by default', function () {
    $peminjaman = Peminjaman::factory()->create();

    expect($peminjaman->car)->toBeInstanceOf(Car::class)
        ->and($peminjaman->divisi)->toBeInstanceOf(Divisi::class)
        ->and($peminjaman->status)->toBe(PeminjamanStatus::Pending)
        ->and($peminjaman->tanggal_mulai)->toBeInstanceOf(CarbonInterface::class)
        ->and($peminjaman->tanggal_selesai)->toBeInstanceOf(CarbonInterface::class)
        ->and($peminjaman->tanggal_selesai->isAfter($peminjaman->tanggal_mulai))->toBeTrue();
});

test('peminjaman status states are applied correctly', function () {
    expect(Peminjaman::factory()->disetujui()->create()->refresh()->status)->toBe(PeminjamanStatus::Disetujui)
        ->and(Peminjaman::factory()->ditolak()->create()->refresh()->status)->toBe(PeminjamanStatus::Ditolak)
        ->and(Peminjaman::factory()->selesai()->create()->refresh()->status)->toBe(PeminjamanStatus::Selesai);
});

test('deleting a divisi keeps the peminjaman record without divisi', function () {
    $peminjaman = Peminjaman::factory()->create();

    $peminjaman->divisi->delete();

    expect($peminjaman->fresh()->divisi_id)->toBeNull()
        ->and(Peminjaman::query()->whereKey($peminjaman->id)->exists())->toBeTrue();
});

test('deleting a car that has peminjaman is restricted', function () {
    $car = Car::factory()->hasPeminjaman(1)->create();

    $car->delete();
})->throws(QueryException::class);
