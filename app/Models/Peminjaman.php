<?php

namespace App\Models;

use App\Enums\PeminjamanStatus;
use Carbon\CarbonInterface;
use Database\Factories\PeminjamanFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property string $nama_peminjam
 * @property string $email_peminjam
 * @property string|null $no_hp
 * @property int|null $divisi_id
 * @property int $car_id
 * @property CarbonInterface $tanggal_mulai
 * @property CarbonInterface $tanggal_selesai
 * @property string $keperluan
 * @property string $lokasi_tujuan
 * @property string $tujuan
 * @property int $km_awal
 * @property int|null $km_akhir
 * @property string $tangki_bbm
 * @property string|null $nama_customer
 * @property string|null $catatan
 * @property PeminjamanStatus $status
 * @property CarbonInterface|null $created_at
 * @property CarbonInterface|null $updated_at
 * @property User|null $user
 */
#[Fillable([
    'nama_peminjam',
    'email_peminjam',
    'no_hp',
    'divisi_id',
    'car_id',
    'tanggal_mulai',
    'tanggal_selesai',
    'keperluan',
    'lokasi_tujuan',
    'tujuan',
    'km_awal',
    'km_akhir',
    'tangki_bbm',
    'nama_customer',
    'catatan',
    'status',
])]
class Peminjaman extends Model
{
    /** @use HasFactory<PeminjamanFactory> */
    use HasFactory;

    protected $table = 'peminjaman';

    protected $attributes = [
        'status' => PeminjamanStatus::Pending->value,
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'tanggal_mulai' => 'datetime',
            'tanggal_selesai' => 'datetime',
            'status' => PeminjamanStatus::class,
        ];
    }

    /**
     * The car that is borrowed.
     *
     * @return BelongsTo<Car, $this>
     */
    public function car(): BelongsTo
    {
        return $this->belongsTo(Car::class);
    }

    /**
     * The division of the borrower.
     *
     * @return BelongsTo<Divisi, $this>
     */
    public function divisi(): BelongsTo
    {
        return $this->belongsTo(Divisi::class);
    }

    /**
     * The user who borrowed (matched by email).
     *
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'email_peminjam', 'email');
    }
}
