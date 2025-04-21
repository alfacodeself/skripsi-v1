<?php

namespace App\Enums;

use Filament\Support\Contracts\{HasColor, HasIcon, HasLabel};

enum CustomerStatus: string implements HasLabel, HasColor, HasIcon
{
    case AKTIF = 'aktif';
    case NONAKTIF = 'tidak aktif';
    case DIPERIKSA = 'diperiksa';
    case PERINGATAN = 'peringatan';
    case DIBLOKIR = 'diblokir';

    public function getLabel(): ?string
    {
        // return $this->name;
        return match ($this) {
            self::AKTIF => 'Aktif',
            self::NONAKTIF => 'Tidak Aktif',
            self::DIPERIKSA => 'Diperiksa',
            self::PERINGATAN => 'Peringatan',
            self::DIBLOKIR => 'Diblokir',
        };
    }
    public function getColor(): string|array|null
    {
        return match ($this) {
            self::AKTIF => 'success',
            self::NONAKTIF => 'gray',
            self::DIPERIKSA => 'primary',
            self::PERINGATAN => 'warning',
            self::DIBLOKIR => 'danger',
        };
    }
    public function getIcon(): ?string
    {
        return match ($this) {
            self::AKTIF => 'heroicon-m-check-circle',
            self::NONAKTIF => 'heroicon-m-power',
            self::DIPERIKSA => 'heroicon-m-clock',
            self::PERINGATAN => 'heroicon-m-exclamation-circle',
            self::DIBLOKIR => 'heroicon-m-x-circle',
        };
    }
}
