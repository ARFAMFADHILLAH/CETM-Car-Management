<?php

namespace App\Enums;

enum NotifikasiTipe: string
{
    case Disetujui = 'disetujui';
    case Ditolak = 'ditolak';
    case Pengingat = 'pengingat';
    case Info = 'info';
}
