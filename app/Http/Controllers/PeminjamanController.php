<?php

namespace App\Http\Controllers;

use App\Enums\CarStatus;
use App\Enums\NotifikasiTipe;
use App\Enums\PeminjamanStatus;
use App\Http\Requests\StorePeminjamanRequest;
use App\Models\Car;
use App\Models\Divisi;
use App\Models\Peminjaman;
use App\Services\NotifikasiService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class PeminjamanController extends Controller
{
    public function __construct(private NotifikasiService $notifikasi) {}

    /**
     * Display peminjaman list (user sees own, admin sees all).
     */
    public function index(): Response
    {
        $user = auth()->user();

        $query = Peminjaman::query()
            ->with(['car', 'divisi'])
            ->orderBy('created_at', 'desc');

        if ($user->isAdmin()) {
            $query->with('user');
        } else {
            $query->where('email_peminjam', $user->email);
        }

        $peminjaman = $query->get();

        $view = $user->isAdmin()
            ? 'admin/peminjaman/Index'
            : 'user/peminjaman/Index';

        return Inertia::render($view, [
            'peminjaman' => $peminjaman,
        ]);
    }

    /**
     * Show the create form with divisi list and available cars.
     */
    public function create(): Response
    {
        $divisiList = Divisi::orderBy('nama_divisi')->get();

        $mobilTersedia = Car::query()
            ->where('status', CarStatus::Tersedia)
            ->orderBy('nama')
            ->get();

        return Inertia::render('user/peminjaman/Create', [
            'divisiList' => $divisiList,
            'mobilTersedia' => $mobilTersedia,
        ]);
    }

    /**
     * Return available cars for a given date range (AJAX).
     */
    public function availableCars(Request $request): JsonResponse
    {
        $request->validate([
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
        ]);

        $tanggalMulai = $request->input('tanggal_mulai');
        $tanggalSelesai = $request->input('tanggal_selesai');

        $activeStatuses = [
            PeminjamanStatus::Pending->value,
            PeminjamanStatus::Disetujui->value,
        ];

        $mobilTersedia = Car::query()
            ->where('status', CarStatus::Tersedia)
            ->whereDoesntHave('peminjaman', function ($query) use ($activeStatuses, $tanggalMulai, $tanggalSelesai) {
                $query->whereIn('status', $activeStatuses)
                    ->where('tanggal_mulai', '<', $tanggalSelesai)
                    ->where('tanggal_selesai', '>', $tanggalMulai);
            })
            ->orderBy('nama')
            ->get(['id', 'nama', 'nomor_plat']);

        return response()->json($mobilTersedia);
    }

    /**
     * Store a newly created peminjaman.
     */
    public function store(StorePeminjamanRequest $request): RedirectResponse
    {
        $data = $request->validated();

        Peminjaman::create([
            'nama_peminjam' => $data['nama_peminjam'],
            'email_peminjam' => $data['email_peminjam'],
            'no_hp' => $data['no_hp'] ?? null,
            'divisi_id' => $data['divisi_id'] ?? null,
            'car_id' => $data['car_id'],
            'tanggal_mulai' => $data['tanggal_mulai'],
            'tanggal_selesai' => $data['tanggal_selesai'],
            'keperluan' => $data['keperluan'],
            'lokasi_tujuan' => $data['lokasi_tujuan'],
            'tujuan' => $data['tujuan'],
            'km_awal' => $data['km_awal'],
            'km_akhir' => $data['km_akhir'] ?? null,
            'tangki_bbm' => $data['tangki_bbm'],
            'nama_customer' => $data['nama_customer'] ?? null,
            'catatan' => $data['catatan'] ?? null,
        ]);

        return to_route('peminjaman.index')
            ->with('success', 'Pengajuan peminjaman berhasil dikirim.');
    }

    /**
     * Approve a peminjaman request and notify the borrower.
     */
    public function approve(Peminjaman $peminjaman): RedirectResponse
    {
        $peminjaman->update(['status' => PeminjamanStatus::Disetujui]);

        if ($user = $peminjaman->user) {
            $this->notifikasi->buat(
                $user,
                'Pengajuan disetujui',
                "Pengajuan peminjaman {$peminjaman->car?->nama} ({$peminjaman->lokasi_tujuan}) telah disetujui.",
                NotifikasiTipe::Disetujui,
            );
        }

        return to_route('approval.index')
            ->with('success', 'Pengajuan peminjaman disetujui.');
    }

    /**
     * Reject a peminjaman request and notify the borrower.
     */
    public function reject(Peminjaman $peminjaman): RedirectResponse
    {
        $peminjaman->update(['status' => PeminjamanStatus::Ditolak]);

        if ($user = $peminjaman->user) {
            $this->notifikasi->buat(
                $user,
                'Pengajuan ditolak',
                "Pengajuan peminjaman {$peminjaman->car?->nama} ({$peminjaman->lokasi_tujuan}) ditolak.",
                NotifikasiTipe::Ditolak,
            );
        }

        return to_route('approval.index')
            ->with('success', 'Pengajuan peminjaman ditolak.');
    }
}
