<?php

namespace Database\Seeders;

use App\Enums\PeminjamanStatus;
use App\Models\Car;
use App\Models\Divisi;
use App\Models\Peminjaman;
use App\Models\User;
use Illuminate\Database\Seeder;

class PeminjamanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::query()->whereNotNull('role_id')->get();
        $divisi = Divisi::query()->pluck('id');
        $cars = Car::query()->pluck('id');

        if ($users->isEmpty() || $divisi->isEmpty() || $cars->isEmpty()) {
            $this->command->line('PeminjamanSeeder dilewati: data users/divisi/cars belum tersedia.');

            return;
        }

        $peminjaman = [
            [
                'keperluan' => 'Kunjungan customer PT Sinar Jaya',
                'lokasi_tujuan' => 'Bandung',
                'tujuan' => 'luar_kota',
                'km_awal' => 12500,
                'km_akhir' => 12650,
                'tangki_bbm' => '3/4',
                'nama_customer' => 'PT Sinar Jaya',
                'status' => PeminjamanStatus::Disetujui,
            ],
            [
                'keperluan' => 'Survey lokasi gudang baru',
                'lokasi_tujuan' => 'Bekasi',
                'tujuan' => 'dalam_kota',
                'km_awal' => 8200,
                'km_akhir' => null,
                'tangki_bbm' => 'full',
                'nama_customer' => null,
                'status' => PeminjamanStatus::Pending,
            ],
            [
                'keperluan' => 'Rapat kontrak kerja sama',
                'lokasi_tujuan' => 'Semarang',
                'tujuan' => 'luar_kota',
                'km_awal' => 34100,
                'km_akhir' => null,
                'tangki_bbm' => '1/2',
                'nama_customer' => 'CV Mitra Usaha',
                'status' => PeminjamanStatus::Pending,
            ],
            [
                'keperluan' => 'Pengiriman dokumen penting',
                'lokasi_tujuan' => 'Jakarta Selatan',
                'tujuan' => 'dalam_kota',
                'km_awal' => 5600,
                'km_akhir' => 5640,
                'tangki_bbm' => '1/4',
                'nama_customer' => null,
                'status' => PeminjamanStatus::Selesai,
            ],
            [
                'keperluan' => 'Dinas luar kota - audit cabang',
                'lokasi_tujuan' => 'Surabaya',
                'tujuan' => 'luar_kota',
                'km_awal' => 21300,
                'km_akhir' => null,
                'tangki_bbm' => 'empty',
                'nama_customer' => null,
                'status' => PeminjamanStatus::Ditolak,
            ],
        ];

        foreach ($peminjaman as $index => $data) {
            $user = $users[$index % $users->count()];
            $tanggalMulai = now()->addDays($index + 1)->setTime(8, 0);
            $tanggalSelesai = $tanggalMulai->copy()->addDays(2)->setTime(17, 0);

            Peminjaman::query()->create([
                'nama_peminjam' => $user->nama,
                'email_peminjam' => $user->email,
                'no_hp' => $user->no_hp,
                'divisi_id' => $divisi[$index % $divisi->count()],
                'car_id' => $cars[$index % $cars->count()],
                'tanggal_mulai' => $tanggalMulai,
                'tanggal_selesai' => $tanggalSelesai,
                'keperluan' => $data['keperluan'],
                'lokasi_tujuan' => $data['lokasi_tujuan'],
                'tujuan' => $data['tujuan'],
                'km_awal' => $data['km_awal'],
                'km_akhir' => $data['km_akhir'],
                'tangki_bbm' => $data['tangki_bbm'],
                'nama_customer' => $data['nama_customer'],
                'catatan' => 'Data contoh dari seeder.',
                'status' => $data['status']->value,
            ]);
        }
    }
}
