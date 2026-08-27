<?php

namespace App\Exports;

use App\Enums\PeminjamanStatus;
use App\Models\Peminjaman;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class PeminjamanExport implements FromCollection, WithHeadings, WithMapping
{
    public function __construct(
        private readonly Request $request,
    ) {}

    /**
     * @return Collection<int, Peminjaman>
     */
    public function collection(): Collection
    {
        $query = Peminjaman::with(['car', 'divisi']);

        if ($this->request->filled('tanggal_mulai')) {
            $query->where('tanggal_mulai', '>=', $this->request->input('tanggal_mulai'));
        }

        if ($this->request->filled('tanggal_selesai')) {
            $query->where('tanggal_selesai', '<=', $this->request->input('tanggal_selesai'));
        }

        if ($this->request->filled('status') && $this->request->input('status') !== 'semua') {
            $query->where('status', $this->request->input('status'));
        }

        if ($this->request->filled('divisi_id')) {
            $query->where('divisi_id', $this->request->input('divisi_id'));
        }

        return $query->latest('tanggal_mulai')->get();
    }

    /**
     * @return list<string>
     */
    public function headings(): array
    {
        return [
            'No',
            'Nama Peminjam',
            'Email',
            'No. HP',
            'Divisi',
            'Mobil',
            'Tanggal Mulai',
            'Tanggal Selesai',
            'Keperluan',
            'Lokasi Tujuan',
            'Customer',
            'Status',
        ];
    }

    /**
     * @param  Peminjaman  $row
     * @return list<string|int|null>
     */
    public function map($row): array
    {
        return [
            $row->id,
            $row->nama_peminjam,
            $row->email_peminjam,
            $row->no_hp,
            $row->divisi?->nama_divisi,
            $row->car?->nama,
            $row->tanggal_mulai->format('d/m/Y H:i'),
            $row->tanggal_selesai->format('d/m/Y H:i'),
            $row->keperluan,
            $row->lokasi_tujuan,
            $row->nama_customer,
            match ($row->status) {
                PeminjamanStatus::Pending => 'Menunggu',
                PeminjamanStatus::Disetujui => 'Disetujui',
                PeminjamanStatus::Ditolak => 'Ditolak',
                PeminjamanStatus::Selesai => 'Selesai',
            },
        ];
    }
}
