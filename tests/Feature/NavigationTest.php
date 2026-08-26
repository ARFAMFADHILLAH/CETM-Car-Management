<?php

use App\Enums\UserRole;
use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

function pengguna(UserRole $role): User
{
    return User::factory()->withRole($role)->create();
}

test('root diarahkan ke dashboard', function () {
    $this->get('/')->assertRedirect(route('dashboard', absolute: false));
});

test('halaman bersama dapat diakses pengguna dan admin', function (UserRole $role, string $routeName, string $component) {
    $user = pengguna($role);

    $this->actingAs($user)
        ->get(route($routeName))
        ->assertOk()
        ->assertInertia(
            fn (Assert $page) => $page->component($component),
        );
})->with([
    'dashboard admin' => [UserRole::Admin, 'dashboard', 'Dashboard'],
    'dashboard pengguna' => [UserRole::User, 'dashboard', 'Dashboard'],
    'jadwal mobil admin' => [UserRole::Admin, 'jadwal.index', 'jadwal/Index'],
    'jadwal mobil pengguna' => [UserRole::User, 'jadwal.index', 'jadwal/Index'],
    'data mobil admin' => [UserRole::Admin, 'mobil.index', 'mobil/Index'],
    'data mobil pengguna' => [UserRole::User, 'mobil.index', 'mobil/Index'],
    'notifikasi admin' => [UserRole::Admin, 'notifikasi.index', 'notifikasi/Index'],
    'notifikasi pengguna' => [UserRole::User, 'notifikasi.index', 'notifikasi/Index'],
    'chatbot admin' => [UserRole::Admin, 'chatbot.show', 'chatbot/Index'],
    'chatbot pengguna' => [UserRole::User, 'chatbot.show', 'chatbot/Index'],
]);

test('halaman khusus pengguna hanya dapat diakses pengguna biasa', function (string $routeName, string $component) {
    $user = pengguna(UserRole::User);

    $this->actingAs($user)
        ->get(route($routeName))
        ->assertOk()
        ->assertInertia(
            fn (Assert $page) => $page->component($component),
        );
})->with([
    'ajukan peminjaman' => ['peminjaman.create', 'user/peminjaman/Create'],
    'daftar pengajuan' => ['peminjaman.index', 'user/peminjaman/Index'],
]);

test('halaman khusus admin hanya dapat diakses admin', function (string $routeName, string $component) {
    $admin = pengguna(UserRole::Admin);

    $this->actingAs($admin)
        ->get(route($routeName))
        ->assertOk()
        ->assertInertia(
            fn (Assert $page) => $page->component($component),
        );
})->with([
    'approve peminjaman' => ['approval.index', 'admin/peminjaman/Index'],
    'manajemen pengguna' => ['manajemen.pengguna', 'admin/pengguna/Index'],
    'manajemen admin' => ['manajemen.admin', 'admin/admin/Index'],
]);

test('admin tidak dapat mengakses halaman khusus pengguna', function (string $routeName) {
    $admin = pengguna(UserRole::Admin);

    $this->actingAs($admin)
        ->get(route($routeName))
        ->assertForbidden();
})->with([
    'ajukan peminjaman' => 'peminjaman.create',
    'daftar pengajuan' => 'peminjaman.index',
]);

test('pengguna tidak dapat mengakses halaman khusus admin', function (string $routeName) {
    $user = pengguna(UserRole::User);

    $this->actingAs($user)
        ->get(route($routeName))
        ->assertForbidden();
})->with([
    'approve peminjaman' => 'approval.index',
    'manajemen pengguna' => 'manajemen.pengguna',
    'manajemen admin' => 'manajemen.admin',
]);

test('tamu diarahkan ke login', function () {
    $this->get(route('jadwal.index'))
        ->assertRedirect(route('login', absolute: false));
});

test('halaman registrasi dinonaktifkan', function () {
    $this->get('/register')->assertNotFound();
});
