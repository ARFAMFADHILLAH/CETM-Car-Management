<?php

namespace App\Exports;

use App\Models\User;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class PenggunaExport implements FromCollection, WithHeadings, WithMapping
{
    /**
     * @return Collection<int, User>
     */
    public function collection(): Collection
    {
        return User::with('role')
            ->whereHas('role', fn ($q) => $q->where('role', 'user'))
            ->withCount('peminjaman as jumlah_peminjaman')
            ->orderBy('nama')
            ->get();
    }

    /**
     * @return list<string>
     */
    public function headings(): array
    {
        return [
            'No',
            'Nama',
            'Email',
            'No. HP',
            'Divisi',
            'Jumlah Peminjaman',
        ];
    }

    /**
     * @param  User  $row
     * @return list<int|string|null>
     */
    public function map($row): array
    {
        return [
            $row->id,
            $row->nama,
            $row->email,
            $row->no_hp,
            $row->role?->role->value,
            $row->jumlah_peminjaman,
        ];
    }
}
