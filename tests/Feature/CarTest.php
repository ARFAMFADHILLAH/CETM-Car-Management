<?php

use App\Enums\UserRole;
use App\Models\Car;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Inertia\Testing\AssertableInertia as Assert;

function admin(): User
{
    return User::factory()->withRole(UserRole::Admin)->create();
}

function penggunaBiasa(): User
{
    return User::factory()->withRole(UserRole::User)->create();
}

test('data mobil dapat diakses pengguna dan admin', function (UserRole $role) {
    $user = match ($role) {
        UserRole::Admin => admin(),
        UserRole::User => penggunaBiasa(),
    };

    $this->actingAs($user)
        ->get(route('mobil.index'))
        ->assertOk()
        ->assertInertia(
            fn (Assert $page) => $page
                ->component('mobil/Index')
                ->has('cars'),
        );
})->with([
    'admin' => UserRole::Admin,
    'pengguna' => UserRole::User,
]);

test('admin dapat menambah mobil dengan foto', function () {
    Storage::fake('public');

    $user = admin();
    $foto = UploadedFile::fake()->image('avanza.jpg', 800, 600)->size(1024);

    $this->actingAs($user)
        ->post(route('mobil.store'), [
            'nama' => 'Toyota Avanza',
            'nomor_plat' => 'B 1234 XYZ',
            'status' => 'tersedia',
            'foto' => $foto,
        ])
        ->assertRedirect();

    $this->assertDatabaseHas('cars', [
        'nama' => 'Toyota Avanza',
        'nomor_plat' => 'B 1234 XYZ',
    ]);

    $car = Car::where('nomor_plat', 'B 1234 XYZ')->first();
    $this->assertNotNull($car->foto);
    Storage::disk('public')->assertExists($car->foto);
});

test('admin dapat menambah mobil tanpa foto', function () {
    $user = admin();

    $this->actingAs($user)
        ->post(route('mobil.store'), [
            'nama' => 'Toyota Avanza',
            'nomor_plat' => 'B 5678 ABC',
            'status' => 'tersedia',
        ])
        ->assertRedirect();

    $this->assertDatabaseHas('cars', [
        'nama' => 'Toyota Avanza',
        'nomor_plat' => 'B 5678 ABC',
        'foto' => null,
    ]);
});

test('admin dapat memperbarui mobil', function () {
    $user = admin();
    $car = Car::factory()->create();

    $this->actingAs($user)
        ->put(route('mobil.update', $car->id), [
            'nama' => 'Toyota Innova',
            'nomor_plat' => $car->nomor_plat,
            'status' => 'di_servis',
        ])
        ->assertRedirect();

    $this->assertDatabaseHas('cars', [
        'id' => $car->id,
        'nama' => 'Toyota Innova',
        'status' => 'di_servis',
    ]);
});

test('admin dapat menghapus mobil', function () {
    $user = admin();
    $car = Car::factory()->create();

    $this->actingAs($user)
        ->delete(route('mobil.destroy', $car->id))
        ->assertRedirect();

    $this->assertDatabaseMissing('cars', ['id' => $car->id]);
});

test('pengguna tidak dapat menambah mobil', function () {
    $user = penggunaBiasa();

    $this->actingAs($user)
        ->post(route('mobil.store'), [
            'nama' => 'Toyota Avanza',
            'nomor_plat' => 'B 1234 XYZ',
            'status' => 'tersedia',
        ])
        ->assertForbidden();
});

test('pengguna tidak dapat memperbarui mobil', function () {
    $user = penggunaBiasa();
    $car = Car::factory()->create();

    $this->actingAs($user)
        ->put(route('mobil.update', $car->id), [
            'nama' => 'Toyota Innova',
            'nomor_plat' => $car->nomor_plat,
            'status' => 'di_servis',
        ])
        ->assertForbidden();
});

test('pengguna tidak dapat menghapus mobil', function () {
    $user = penggunaBiasa();
    $car = Car::factory()->create();

    $this->actingAs($user)
        ->delete(route('mobil.destroy', $car->id))
        ->assertForbidden();
});

test('validasi store menolak data tidak lengkap', function () {
    $user = admin();

    $this->actingAs($user)
        ->post(route('mobil.store'), [])
        ->assertSessionHasErrors(['nama', 'nomor_plat', 'status']);
});

test('validasi store menolak foto non-gambar', function () {
    Storage::fake('public');

    $user = admin();
    $file = UploadedFile::fake()->create('document.pdf', 100, 'application/pdf');

    $this->actingAs($user)
        ->post(route('mobil.store'), [
            'nama' => 'Toyota Avanza',
            'nomor_plat' => 'B 1234 XYZ',
            'status' => 'tersedia',
            'foto' => $file,
        ])
        ->assertSessionHasErrors(['foto']);
});
