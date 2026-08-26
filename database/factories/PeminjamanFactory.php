<?php

namespace Database\Factories;

use App\Enums\PeminjamanStatus;
use App\Models\Car;
use App\Models\Divisi;
use App\Models\Peminjaman;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Peminjaman>
 */
class PeminjamanFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $tanggalMulai = fake()->dateTimeBetween('+1 day', '+2 months');

        return [
            'nama_peminjam' => fake()->name(),
            'email_peminjam' => fake()->safeEmail(),
            'no_hp' => fake()->phoneNumber(),
            'divisi_id' => Divisi::factory(),
            'car_id' => Car::factory(),
            'tanggal_mulai' => $tanggalMulai,
            'tanggal_selesai' => fake()->dateTimeBetween($tanggalMulai, $tanggalMulai->format('Y-m-d H:i:s').' +7 days'),
            'kegiatan' => fake()->randomElement([
                'Survey lokasi proyek',
                'Kunjungan customer',
                'Rapat dengan klien',
                'Pengiriman dokumen',
                'Dinas luar kota',
                'Monitoring cabang',
            ]),
            'lokasi_tujuan' => fake()->city(),
            'nama_customer' => fake()->optional(0.8)->company(),
            'catatan' => fake()->optional(0.7)->sentence(),
            'status' => PeminjamanStatus::Pending->value,
        ];
    }

    /**
     * Indicate that the peminjaman is approved.
     */
    public function disetujui(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => PeminjamanStatus::Disetujui->value,
        ]);
    }

    /**
     * Indicate that the peminjaman is rejected.
     */
    public function ditolak(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => PeminjamanStatus::Ditolak->value,
        ]);
    }

    /**
     * Indicate that the peminjaman is finished.
     */
    public function selesai(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => PeminjamanStatus::Selesai->value,
        ]);
    }
}
