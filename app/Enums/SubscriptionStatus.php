<?php

namespace App\Enums;

use Filament\Support\Contracts\{HasColor, HasIcon, HasLabel};

enum SubscriptionStatus: string implements HasLabel, HasColor, HasIcon
{
    case DIPERIKSA = 'diperiksa';
    case DISETUJUI = 'disetujui';
    case DITOLAK = 'ditolak';
    case AKTIF = 'aktif';
    case NONAKTIF = 'tidak aktif';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::DISETUJUI => 'Disetujui',
            self::DITOLAK => 'Ditolak',
            self::DIPERIKSA => 'Diperiksa',
            self::AKTIF => 'Aktif',
            self::NONAKTIF => 'Tidak Aktif',
        };
    }
    public function getColor(): string|array|null
    {
        return match ($this) {
            self::DISETUJUI => 'success',
            self::DIPERIKSA => 'warning',
            self::DITOLAK => 'danger',
            self::AKTIF => 'primary',
            self::NONAKTIF => 'gray',
        };
    }
    public function getIcon(): ?string
    {
        return match ($this) {
            self::DISETUJUI => 'heroicon-m-check-circle',
            self::DIPERIKSA => 'heroicon-m-clock',
            self::DITOLAK => 'heroicon-m-x-circle',
            self::AKTIF => 'heroicon-m-shield-check',
            self::NONAKTIF => 'heroicon-m-power',
        };
    }
}
