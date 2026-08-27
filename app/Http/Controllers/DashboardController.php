<?php

namespace App\Http\Controllers;

use App\Enums\CarStatus;
use App\Enums\PeminjamanStatus;
use App\Models\Car;
use App\Models\Peminjaman;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    /**
     * Display the dashboard with real statistics and upcoming schedules.
     */
    public function index(): Response
    {
        $user = auth()->user();

        $mobilTersedia = Car::query()
            ->where('status', CarStatus::Tersedia)
            ->count();

        $mobilDiservis = Car::query()
            ->whereIn('status', [CarStatus::DiServis, CarStatus::TidakTersedia])
            ->count();

        $totalMobil = Car::query()->count();

        if ($user->isAdmin()) {
            $pengajuanPending = Peminjaman::query()
                ->with(['car', 'divisi', 'user'])
                ->where('status', PeminjamanStatus::Pending)
                ->orderBy('created_at')
                ->get();

            $jadwalTerdekat = collect();

            return Inertia::render('Dashboard', [
                'totalMobil' => $totalMobil,
                'mobilTersedia' => $mobilTersedia,
                'mobilDiservis' => $mobilDiservis,
                'pengajuanPending' => $pengajuanPending,
                'jadwalTerdekat' => $jadwalTerdekat,
                'isAdmin' => true,
            ]);
        }

        $jadwalTerdekat = Peminjaman::query()
            ->with(['car', 'divisi'])
            ->where('email_peminjam', $user->email)
            ->where('status', PeminjamanStatus::Disetujui)
            ->where('tanggal_selesai', '>=', now())
            ->orderBy('tanggal_mulai')
            ->limit(4)
            ->get();

        return Inertia::render('Dashboard', [
            'totalMobil' => $totalMobil,
            'mobilTersedia' => $mobilTersedia,
            'mobilDiservis' => $mobilDiservis,
            'pengajuanPending' => collect(),
            'jadwalTerdekat' => $jadwalTerdekat,
            'isAdmin' => false,
        ]);
    }
}
