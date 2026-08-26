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
            'Managing director',
            'Sales Engineer',
            'Sales Excecutive',
            'Supervisor Sales Engineer',
            'Marketing Communication',
            'Technical Engineer',
            'Logistic',
            'Finance & Logistic Manager',
            'Finance & Accounting',
            'Accounting Staff',
            'Delivery Man'
        ];

        foreach ($divisi as $namaDivisi) {
            Divisi::query()->firstOrCreate(['nama_divisi' => $namaDivisi]);
        }
    }
}
