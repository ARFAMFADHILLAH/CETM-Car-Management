<?php

namespace App\Http\Controllers;

use App\Exports\MobilExport;
use App\Exports\PeminjamanExport;
use App\Exports\PenggunaExport;
use App\Models\Car;
use App\Models\Divisi;
use App\Models\Peminjaman;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ReportController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('admin/laporan/Index');
    }

    public function peminjaman(Request $request): Response
    {
        $query = Peminjaman::with(['car', 'divisi']);

        if ($request->filled('tanggal_mulai')) {
            $query->where('tanggal_mulai', '>=', $request->input('tanggal_mulai'));
        }

        if ($request->filled('tanggal_selesai')) {
            $query->where('tanggal_selesai', '<=', $request->input('tanggal_selesai'));
        }

        if ($request->filled('status') && $request->input('status') !== 'semua') {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('divisi_id')) {
            $query->where('divisi_id', $request->input('divisi_id'));
        }

        $data = $query->latest('tanggal_mulai')->get();
        $divisi = Divisi::orderBy('nama_divisi')->get();

        return Inertia::render('admin/laporan/Index', [
            'tab' => 'peminjaman',
            'laporanPeminjaman' => $data,
            'divisiList' => $divisi,
            'filters' => $request->only(['tanggal_mulai', 'tanggal_selesai', 'status', 'divisi_id']),
        ]);
    }

    public function mobil(): Response
    {
        $data = Car::withCount('peminjaman')->orderBy('nama')->get();

        return Inertia::render('admin/laporan/Index', [
            'tab' => 'mobil',
            'laporanMobil' => $data,
        ]);
    }

    public function pengguna(): Response
    {
        $data = User::with('role')
            ->whereHas('role', fn ($q) => $q->where('role', 'user'))
            ->withCount('peminjaman as jumlah_peminjaman')
            ->orderBy('nama')
            ->get();

        return Inertia::render('admin/laporan/Index', [
            'tab' => 'pengguna',
            'laporanPengguna' => $data,
        ]);
    }

    public function exportPdf(Request $request, string $type): \Symfony\Component\HttpFoundation\Response
    {
        $data = $this->getExportData($request, $type);

        $pdf = Pdf::loadView("reports.{$type}", [
            'data' => $data['data'],
            'title' => $data['title'],
            'filters' => $data['filters'],
            'generatedAt' => now()->format('d/m/Y H:i'),
        ]);

        $pdf->setPaper('a4', 'landscape');

        return $pdf->download("laporan-{$type}-".now()->format('Y-m-d').'.pdf');
    }

    public function exportExcel(Request $request, string $type): BinaryFileResponse
    {
        $export = match ($type) {
            'peminjaman' => new PeminjamanExport($request),
            'mobil' => new MobilExport,
            'pengguna' => new PenggunaExport,
            default => abort(404),
        };

        return Excel::download($export, "laporan-{$type}-".now()->format('Y-m-d').'.xlsx');
    }

    /**
     * @return array<string, mixed>
     */
    private function getExportData(Request $request, string $type): array
    {
        return match ($type) {
            'peminjaman' => $this->getPeminjamanData($request),
            'mobil' => $this->getMobilData(),
            'pengguna' => $this->getPenggunaData(),
            default => abort(404),
        };
    }

    /**
     * @return array{data: Collection<int, Peminjaman>, title: string, filters: array<string, string>}
     */
    private function getPeminjamanData(Request $request): array
    {
        $query = Peminjaman::with(['car', 'divisi']);

        if ($request->filled('tanggal_mulai')) {
            $query->where('tanggal_mulai', '>=', $request->input('tanggal_mulai'));
        }

        if ($request->filled('tanggal_selesai')) {
            $query->where('tanggal_selesai', '<=', $request->input('tanggal_selesai'));
        }

        if ($request->filled('status') && $request->input('status') !== 'semua') {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('divisi_id')) {
            $query->where('divisi_id', $request->input('divisi_id'));
        }

        $filters = $request->only(['tanggal_mulai', 'tanggal_selesai', 'status', 'divisi_id']);

        return [
            'data' => $query->latest('tanggal_mulai')->get(),
            'title' => 'Laporan Peminjaman',
            'filters' => $filters,
        ];
    }

    /**
     * @return array{data: Collection<int, Car>, title: string, filters: array<string, string>}
     */
    private function getMobilData(): array
    {
        return [
            'data' => Car::withCount('peminjaman')->orderBy('nama')->get(),
            'title' => 'Laporan Mobil',
            'filters' => [],
        ];
    }

    /**
     * @return array{data: Collection<int, User>, title: string, filters: array<string, string>}
     */
    private function getPenggunaData(): array
    {
        return [
            'data' => User::with('role')
                ->whereHas('role', fn ($q) => $q->where('role', 'user'))
                ->withCount('peminjaman as jumlah_peminjaman')
                ->orderBy('nama')
                ->get(),
            'title' => 'Laporan Pengguna',
            'filters' => [],
        ];
    }
}
