<?php

namespace App\Enums;

enum PeminjamanStatus: string
{
    case Pending = 'pending';
    case Disetujui = 'disetujui';
    case Ditolak = 'ditolak';
    case Selesai = 'selesai';
}
