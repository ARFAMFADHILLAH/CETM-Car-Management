<?php

namespace App\Exports;

use App\Enums\CarStatus;
use App\Models\Car;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class MobilExport implements FromCollection, WithHeadings, WithMapping
{
    /**
     * @return Collection<int, Car>
     */
    public function collection(): Collection
    {
        return Car::withCount('peminjaman')->orderBy('nama')->get();
    }

    /**
     * @return list<string>
     */
    public function headings(): array
    {
        return [
            'No',
            'Nama Mobil',
            'Nomor Plat',
            'Status',
            'Jumlah Peminjaman',
        ];
    }

    /**
     * @param  Car  $row
     * @return list<string|int|null>
     */
    public function map($row): array
    {
        return [
            $row->id,
            $row->nama,
            $row->nomor_plat,
            match ($row->status) {
                CarStatus::Tersedia => 'Tersedia',
                CarStatus::TidakTersedia => 'Tidak Tersedia',
                CarStatus::DiServis => 'Di Servis',
            },
            $row->peminjaman_count,
        ];
    }
}
