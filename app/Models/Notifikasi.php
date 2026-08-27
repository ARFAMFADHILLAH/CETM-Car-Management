<?php

namespace App\Models;

use App\Enums\NotifikasiTipe;
use Database\Factories\NotifikasiFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $user_id
 * @property string $judul
 * @property string $pesan
 * @property NotifikasiTipe $tipe
 * @property bool $dibaca
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property User $user
 */
#[Fillable(['user_id', 'judul', 'pesan', 'tipe', 'dibaca'])]
class Notifikasi extends Model
{
    /** @use HasFactory<NotifikasiFactory> */
    use HasFactory;

    protected $table = 'notifikasi';

    protected $attributes = [
        'dibaca' => false,
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'dibaca' => 'boolean',
            'tipe' => NotifikasiTipe::class,
        ];
    }

    /**
     * The user that owns the notification.
     *
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
