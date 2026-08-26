<?php

use App\Http\Controllers\CarController;
use App\Http\Controllers\ManajemenPenggunaController;
use App\Http\Controllers\ReportController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', 'dashboard')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::inertia('dashboard', 'Dashboard')->name('dashboard');

    // Halaman bersama (pengguna & admin)...
    Route::middleware('role:user,admin')->group(function () {
        Route::inertia('jadwal-mobil', 'jadwal/Index')->name('jadwal.index');
        Route::get('data-mobil', [CarController::class, 'index'])->name('mobil.index');
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
        Route::get('manajemen-pengguna', [ManajemenPenggunaController::class, 'index'])->name('manajemen.pengguna');
        Route::post('manajemen-pengguna', [ManajemenPenggunaController::class, 'store'])->name('manajemen.pengguna.store');
        Route::put('manajemen-pengguna/{user}', [ManajemenPenggunaController::class, 'update'])->name('manajemen.pengguna.update');
        Route::delete('manajemen-pengguna/{user}', [ManajemenPenggunaController::class, 'destroy'])->name('manajemen.pengguna.destroy');
        Route::inertia('manajemen-admin', 'admin/admin/Index')->name('manajemen.admin');

        // CRUD Mobil (admin only)...
        Route::post('data-mobil', [CarController::class, 'store'])->name('mobil.store');
        Route::put('data-mobil/{car}', [CarController::class, 'update'])->name('mobil.update');
        Route::delete('data-mobil/{car}', [CarController::class, 'destroy'])->name('mobil.destroy');

        // Laporan (admin only)...
        Route::get('laporan', [ReportController::class, 'index'])->name('laporan.index');
        Route::get('laporan/peminjaman', [ReportController::class, 'peminjaman'])->name('laporan.peminjaman');
        Route::get('laporan/mobil', [ReportController::class, 'mobil'])->name('laporan.mobil');
        Route::get('laporan/pengguna', [ReportController::class, 'pengguna'])->name('laporan.pengguna');
        Route::get('laporan/{type}/export/pdf', [ReportController::class, 'exportPdf'])->name('laporan.export.pdf');
        Route::get('laporan/{type}/export/excel', [ReportController::class, 'exportExcel'])->name('laporan.export.excel');
    });
});

require __DIR__.'/settings.php';
