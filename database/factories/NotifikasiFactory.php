<?php

namespace Database\Factories;

use App\Enums\NotifikasiTipe;
use App\Models\Notifikasi;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Notifikasi>
 */
class NotifikasiFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'judul' => fake()->sentence(3),
            'pesan' => fake()->sentence(),
            'tipe' => NotifikasiTipe::Info->value,
            'dibaca' => false,
        ];
    }

    /**
     * Indicate that the notification is unread.
     */
    public function belumDibaca(): static
    {
        return $this->state(fn (array $attributes) => [
            'dibaca' => false,
        ]);
    }

    /**
     * Indicate that the notification has been read.
     */
    public function sudahDibaca(): static
    {
        return $this->state(fn (array $attributes) => [
            'dibaca' => true,
        ]);
    }
}
