<?php

use App\Enums\UserRole;
use App\Models\Divisi;
use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

test('admin dapat mengakses halaman manajemen pengguna', function () {
    $admin = User::factory()->withRole(UserRole::Admin)->create();

    $this->actingAs($admin)
        ->get(route('manajemen.pengguna'))
        ->assertOk()
        ->assertInertia(
            fn (Assert $page) => $page
                ->component('admin/pengguna/Index')
                ->has('users')
                ->has('divisiList'),
        );
});

test('pengguna tidak dapat mengakses halaman manajemen pengguna', function () {
    $user = User::factory()->withRole(UserRole::User)->create();

    $this->actingAs($user)
        ->get(route('manajemen.pengguna'))
        ->assertForbidden();
});

test('admin dapat menambah pengguna baru', function () {
    $admin = User::factory()->withRole(UserRole::Admin)->create();
    $divisi = Divisi::factory()->create();

    $this->actingAs($admin)
        ->post(route('manajemen.pengguna.store'), [
            'nama' => 'Test User',
            'email' => 'testuser@example.com',
            'password' => 'password123',
            'no_hp' => '081234567890',
            'divisi_id' => $divisi->id,
        ])
        ->assertRedirect();

    $this->assertDatabaseHas('users', [
        'email' => 'testuser@example.com',
        'nama' => 'Test User',
    ]);
});

test('validasi gagal jika email sudah digunakan saat tambah', function () {
    $admin = User::factory()->withRole(UserRole::Admin)->create();
    User::factory()->create(['email' => 'exists@example.com']);

    $this->actingAs($admin)
        ->post(route('manajemen.pengguna.store'), [
            'nama' => 'Duplikat',
            'email' => 'exists@example.com',
            'password' => 'password123',
        ])
        ->assertSessionHasErrors('email');
});

test('admin dapat memperbarui data pengguna', function () {
    $admin = User::factory()->withRole(UserRole::Admin)->create();
    $user = User::factory()->withRole(UserRole::User)->create();

    $this->actingAs($admin)
        ->put(route('manajemen.pengguna.update', $user->id), [
            'nama' => 'Nama Baru',
            'email' => $user->email,
        ])
        ->assertRedirect();

    $this->assertDatabaseHas('users', [
        'id' => $user->id,
        'nama' => 'Nama Baru',
    ]);
});

test('admin dapat menghapus pengguna', function () {
    $admin = User::factory()->withRole(UserRole::Admin)->create();
    $user = User::factory()->withRole(UserRole::User)->create();

    $this->actingAs($admin)
        ->delete(route('manajemen.pengguna.destroy', $user->id))
        ->assertRedirect();

    $this->assertDatabaseMissing('users', ['id' => $user->id]);
});
