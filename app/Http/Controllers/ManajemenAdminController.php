<?php

namespace App\Http\Controllers;

use App\Http\Requests\ResetPasswordAdminRequest;
use App\Http\Requests\StoreAdminRequest;
use App\Http\Requests\UpdateAdminRequest;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class ManajemenAdminController extends Controller
{
    /**
     * Display a listing of admin accounts.
     */
    public function index(): Response
    {
        $admins = User::query()
            ->with(['role'])
            ->whereHas('role', fn ($q) => $q->where('role', 'admin'))
            ->orderBy('nama')
            ->get();

        return Inertia::render('admin/admin/Index', [
            'admins' => $admins,
        ]);
    }

    /**
     * Store a newly created admin account.
     */
    public function store(StoreAdminRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $role = Role::where('role', 'admin')->first();

        User::create([
            'nama' => $data['nama'],
            'email' => $data['email'],
            'password' => $data['password'],
            'no_hp' => $data['no_hp'] ?? null,
            'role_id' => $role?->id,
            'email_verified_at' => now(),
        ]);

        return to_route('manajemen.admin')
            ->with('success', 'Admin berhasil ditambahkan.');
    }

    /**
     * Update the specified admin account.
     */
    public function update(UpdateAdminRequest $request, User $user): RedirectResponse
    {
        $data = $request->validated();

        $user->update([
            'nama' => $data['nama'],
            'email' => $data['email'],
            'no_hp' => $data['no_hp'] ?? null,
        ]);

        return to_route('manajemen.admin')
            ->with('success', 'Data admin berhasil diperbarui.');
    }

    /**
     * Remove the specified admin account.
     */
    public function destroy(User $user): RedirectResponse
    {
        $user->delete();

        return to_route('manajemen.admin')
            ->with('success', 'Admin berhasil dihapus.');
    }

    /**
     * Reset the specified admin's password.
     */
    public function resetPassword(ResetPasswordAdminRequest $request, User $user): RedirectResponse
    {
        $user->update([
            'password' => $request->password,
        ]);

        return to_route('manajemen.admin')
            ->with('success', 'Password admin berhasil direset.');
    }
}
