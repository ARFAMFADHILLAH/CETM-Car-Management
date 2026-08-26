<?php

namespace Database\Factories;

use App\Enums\CarStatus;
use App\Models\Car;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Car>
 */
class CarFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nama' => fake()->randomElement([
                'Toyota Avanza',
                'Toyota Kijang Innova',
                'Toyota Hiace',
                'Mitsubishi Xpander',
                'Honda Brio',
                'Suzuki Ertiga',
                'Isuzu Elf',
                'Daihatsu Sigra',
            ]),
            'nomor_plat' => sprintf(
                '%s %s %s',
                fake()->unique()->regexify('[A-Z]{1,2}'),
                fake()->numberBetween(1, 9999),
                fake()->regexify('[A-Z]{3}'),
            ),
            'status' => CarStatus::Tersedia->value,
        ];
    }

    /**
     * Indicate that the car is not available.
     */
    public function tidakTersedia(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => CarStatus::TidakTersedia->value,
        ]);
    }

    /**
     * Indicate that the car is being serviced.
     */
    public function diServis(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => CarStatus::DiServis->value,
        ]);
    }
}
