<?php

namespace App\Models;

use App\Enums\CarStatus;
use Database\Factories\CarFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $nama
 * @property string $nomor_plat
 * @property CarStatus $status
 * @property string|null $foto
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable(['nama', 'nomor_plat', 'status', 'foto'])]
class Car extends Model
{
    /** @use HasFactory<CarFactory> */
    use HasFactory;

    protected $attributes = [
        'status' => CarStatus::Tersedia->value,
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => CarStatus::class,
        ];
    }

    /**
     * The peminjaman that belong to the car.
     *
     * @return HasMany<Peminjaman, $this>
     */
    public function peminjaman(): HasMany
    {
        return $this->hasMany(Peminjaman::class);
    }
}
