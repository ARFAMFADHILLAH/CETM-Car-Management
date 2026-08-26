<?php

namespace App\Models;

use Database\Factories\DivisiFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $nama_divisi
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable(['nama_divisi'])]
class Divisi extends Model
{
    /** @use HasFactory<DivisiFactory> */
    use HasFactory;

    protected $table = 'divisi';

    /**
     * The peminjaman that belong to the divisi.
     *
     * @return HasMany<Peminjaman, $this>
     */
    public function peminjaman(): HasMany
    {
        return $this->hasMany(Peminjaman::class);
    }
}
