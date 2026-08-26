<?php

namespace Database\Seeders;

use App\Models\Car;
use Illuminate\Database\Seeder;

class CarSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $cars = [
            ['nama' => 'Toyota Avanza', 'nomor_plat' => 'B 1234 XYZ'],
            ['nama' => 'Toyota Kijang Innova', 'nomor_plat' => 'B 2345 ABC'],
            ['nama' => 'Toyota Hiace', 'nomor_plat' => 'B 7512 TRK'],
            ['nama' => 'Mitsubishi Xpander', 'nomor_plat' => 'D 4567 KLM'],
            ['nama' => 'Suzuki Ertiga', 'nomor_plat' => 'L 8901 MNO'],
        ];

        foreach ($cars as $car) {
            Car::query()->firstOrCreate(['nomor_plat' => $car['nomor_plat']], $car);
        }
    }
}
