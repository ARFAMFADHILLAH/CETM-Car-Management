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
                'kegiatan' => 'Kunjungan customer PT Sinar Jaya',
                'lokasi_tujuan' => 'Bandung',
                'nama_customer' => 'PT Sinar Jaya',
                'status' => PeminjamanStatus::Disetujui,
            ],
            [
                'kegiatan' => 'Survey lokasi gudang baru',
                'lokasi_tujuan' => 'Bekasi',
                'nama_customer' => null,
                'status' => PeminjamanStatus::Pending,
            ],
            [
                'kegiatan' => 'Rapat kontrak kerja sama',
                'lokasi_tujuan' => 'Semarang',
                'nama_customer' => 'CV Mitra Usaha',
                'status' => PeminjamanStatus::Pending,
            ],
            [
                'kegiatan' => 'Pengiriman dokumen penting',
                'lokasi_tujuan' => 'Jakarta Selatan',
                'nama_customer' => null,
                'status' => PeminjamanStatus::Selesai,
            ],
            [
                'kegiatan' => 'Dinas luar kota - audit cabang',
                'lokasi_tujuan' => 'Surabaya',
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
                'kegiatan' => $data['kegiatan'],
                'lokasi_tujuan' => $data['lokasi_tujuan'],
                'nama_customer' => $data['nama_customer'],
                'catatan' => 'Data contoh dari seeder.',
                'status' => $data['status']->value,
            ]);
        }
    }
}
