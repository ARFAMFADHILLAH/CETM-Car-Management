<?php

namespace App\Enums;

enum CarStatus: string
{
    case Tersedia = 'tersedia';
    case TidakTersedia = 'tidak_tersedia';
    case DiServis = 'di_servis';
}
