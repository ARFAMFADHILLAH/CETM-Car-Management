<?php

use Illuminate\Support\Facades\Route;

Route::redirect('/', 'dashboard')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::inertia('dashboard', 'Dashboard')->name('dashboard');

    // Halaman bersama (pengguna & admin)...
    Route::middleware('role:user,admin')->group(function () {
        Route::inertia('jadwal-mobil', 'jadwal/Index')->name('jadwal.index');
        Route::inertia('data-mobil', 'mobil/Index')->name('mobil.index');
        Route::inertia('notifikasi', 'notifikasi/Index')->name('notifikasi.index');
        Route::inertia('chatbot', 'chatbot/Index')->name('chatbot.show');
    });

    // Halaman khusus pengguna...
    Route::middleware('role:user')->group(function () {
        Route::inertia('ajukan-peminjaman', 'user/peminjaman/Create')->name('peminjaman.create');
        Route::inertia('daftar-pengajuan', 'user/peminjaman/Index')->name('peminjaman.index');
    });

    // Halaman khusus admin...
    Route::middleware('role:admin')->group(function () {
        Route::inertia('approve-peminjaman', 'admin/peminjaman/Index')->name('approval.index');
        Route::inertia('manajemen-pengguna', 'admin/pengguna/Index')->name('manajemen.pengguna');
        Route::inertia('manajemen-admin', 'admin/admin/Index')->name('manajemen.admin');
    });
});

require __DIR__.'/settings.php';
