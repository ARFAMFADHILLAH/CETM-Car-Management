<?php

namespace App\Services;

use App\Enums\NotifikasiTipe;
use App\Models\Notifikasi;
use App\Models\User;

class NotifikasiService
{
    /**
     * Create a new notification for the given user.
     */
    public function buat(User $user, string $judul, string $pesan, NotifikasiTipe $tipe = NotifikasiTipe::Info): Notifikasi
    {
        return Notifikasi::create([
            'user_id' => $user->id,
            'judul' => $judul,
            'pesan' => $pesan,
            'tipe' => $tipe->value,
            'dibaca' => false,
        ]);
    }
}
