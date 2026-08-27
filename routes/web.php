<?php

use App\Http\Controllers\CarController;
use App\Http\Controllers\ChatbotController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\JadwalController;
use App\Http\Controllers\ManajemenAdminController;
use App\Http\Controllers\ManajemenPenggunaController;
use App\Http\Controllers\NotifikasiController;
use App\Http\Controllers\PeminjamanController;
use App\Http\Controllers\ReportController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', 'dashboard')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Halaman bersama (pengguna & admin)...
    Route::middleware('role:user,admin')->group(function () {
        Route::get('jadwal-mobil', [JadwalController::class, 'index'])->name('jadwal.index');
        Route::get('data-mobil', [CarController::class, 'index'])->name('mobil.index');
        Route::get('notifikasi', [NotifikasiController::class, 'index'])->name('notifikasi.index');
        Route::put('notifikasi/{notifikasi}/baca', [NotifikasiController::class, 'markRead'])->name('notifikasi.read');
        Route::put('notifikasi/baca-semua', [NotifikasiController::class, 'markAllRead'])->name('notifikasi.readAll');
        Route::inertia('chatbot', 'chatbot/Index')->name('chatbot.show');
        Route::post('api/chatbot', [ChatbotController::class, 'chat'])->name('chatbot.chat');
    });

    // Halaman khusus pengguna...
    Route::middleware('role:user')->group(function () {
        Route::get('ajukan-peminjaman', [PeminjamanController::class, 'create'])->name('peminjaman.create');
        Route::post('ajukan-peminjaman', [PeminjamanController::class, 'store'])->name('peminjaman.store');
        Route::get('daftar-pengajuan', [PeminjamanController::class, 'index'])->name('peminjaman.index');
        Route::get('api/mobil-tersedia', [PeminjamanController::class, 'availableCars'])->name('peminjaman.availableCars');
    });

    // Halaman khusus admin...
    Route::middleware('role:admin')->group(function () {
        Route::get('approve-peminjaman', [PeminjamanController::class, 'index'])->name('approval.index');
        Route::put('approve-peminjaman/{peminjaman}/setujui', [PeminjamanController::class, 'approve'])->name('approval.approve');
        Route::put('approve-peminjaman/{peminjaman}/tolak', [PeminjamanController::class, 'reject'])->name('approval.reject');
        Route::get('manajemen-pengguna', [ManajemenPenggunaController::class, 'index'])->name('manajemen.pengguna');
        Route::post('manajemen-pengguna', [ManajemenPenggunaController::class, 'store'])->name('manajemen.pengguna.store');
        Route::put('manajemen-pengguna/{user}', [ManajemenPenggunaController::class, 'update'])->name('manajemen.pengguna.update');
        Route::put('manajemen-pengguna/{user}/password', [ManajemenPenggunaController::class, 'resetPassword'])->name('manajemen.pengguna.resetPassword');
        Route::delete('manajemen-pengguna/{user}', [ManajemenPenggunaController::class, 'destroy'])->name('manajemen.pengguna.destroy');
        Route::get('manajemen-admin', [ManajemenAdminController::class, 'index'])->name('manajemen.admin');
        Route::post('manajemen-admin', [ManajemenAdminController::class, 'store'])->name('manajemen.admin.store');
        Route::put('manajemen-admin/{user}', [ManajemenAdminController::class, 'update'])->name('manajemen.admin.update');
        Route::put('manajemen-admin/{user}/password', [ManajemenAdminController::class, 'resetPassword'])->name('manajemen.admin.resetPassword');
        Route::delete('manajemen-admin/{user}', [ManajemenAdminController::class, 'destroy'])->name('manajemen.admin.destroy');

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
