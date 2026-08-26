<?php

namespace Database\Seeders;

use App\Models\Divisi;
use Illuminate\Database\Seeder;

class DivisiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $divisi = [
            'Operasional',
            'Keuangan',
            'Sumber Daya Manusia',
            'Pemasaran',
            'Teknik',
            'Logistik',
        ];

        foreach ($divisi as $namaDivisi) {
            Divisi::query()->firstOrCreate(['nama_divisi' => $namaDivisi]);
        }
    }
}
