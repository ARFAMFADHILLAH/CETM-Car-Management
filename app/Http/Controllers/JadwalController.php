<?php

namespace App\Http\Controllers;

use App\Enums\PeminjamanStatus;
use App\Models\Peminjaman;
use Inertia\Inertia;
use Inertia\Response;

class JadwalController extends Controller
{
    /**
     * Display the monthly schedule of approved and finished peminjaman.
     */
    public function index(): Response
    {
        $peminjaman = Peminjaman::query()
            ->with(['car', 'divisi'])
            ->whereIn('status', [PeminjamanStatus::Disetujui, PeminjamanStatus::Selesai])
            ->orderBy('tanggal_mulai')
            ->get();

        return Inertia::render('jadwal/Index', [
            'peminjaman' => $peminjaman,
        ]);
    }
}
