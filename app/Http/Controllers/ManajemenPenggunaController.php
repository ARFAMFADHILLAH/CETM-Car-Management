<?php

namespace App\Http\Controllers;

use App\Http\Requests\ResetPasswordPenggunaRequest;
use App\Http\Requests\StorePenggunaRequest;
use App\Http\Requests\UpdatePenggunaRequest;
use App\Models\Divisi;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class ManajemenPenggunaController extends Controller
{
    /**
     * Display a listing of user accounts.
     */
    public function index(): Response
    {
        $users = User::query()
            ->with(['role', 'divisi'])
            ->whereHas('role', fn ($q) => $q->where('role', 'user'))
            ->orderBy('nama')
            ->get();

        $divisiList = Divisi::orderBy('nama_divisi')->get();

        return Inertia::render('admin/pengguna/Index', [
            'users' => $users,
            'divisiList' => $divisiList,
        ]);
    }

    /**
     * Store a newly created user account.
     */
    public function store(StorePenggunaRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $role = Role::where('role', 'user')->first();

        User::create([
            'nama' => $data['nama'],
            'email' => $data['email'],
            'password' => $data['password'],
            'no_hp' => $data['no_hp'] ?? null,
            'divisi_id' => $data['divisi_id'] ?? null,
            'role_id' => $role?->id,
            'email_verified_at' => now(),
        ]);

        return to_route('manajemen.pengguna')
            ->with('success', 'Pengguna berhasil ditambahkan.');
    }

    /**
     * Update the specified user account.
     */
    public function update(UpdatePenggunaRequest $request, User $user): RedirectResponse
    {
        $data = $request->validated();

        $user->update([
            'nama' => $data['nama'],
            'email' => $data['email'],
            'no_hp' => $data['no_hp'] ?? null,
            'divisi_id' => $data['divisi_id'] ?? null,
        ]);

        return to_route('manajemen.pengguna')
            ->with('success', 'Data pengguna berhasil diperbarui.');
    }

    /**
     * Reset the specified user's password.
     */
    public function resetPassword(ResetPasswordPenggunaRequest $request, User $user): RedirectResponse
    {
        $user->update([
            'password' => $request->password,
        ]);

        return to_route('manajemen.pengguna')
            ->with('success', 'Password pengguna berhasil direset.');
    }

    /**
     * Remove the specified user account.
     */
    public function destroy(User $user): RedirectResponse
    {
        $user->delete();

        return to_route('manajemen.pengguna')
            ->with('success', 'Pengguna berhasil dihapus.');
    }
}
