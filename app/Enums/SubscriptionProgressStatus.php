<?php

namespace App\Enums;

use Filament\Support\Contracts\{HasColor, HasIcon, HasLabel};

enum SubscriptionProgressStatus: string implements HasLabel, HasColor, HasIcon
{
    case DIPROSES = 'diproses';
    case SELESAI = 'selesai';
    case GAGAL = 'gagal';
    case MENUNGGU = 'menunggu';

    public function getLabel(): ?string
    {
        // return $this->name;
        return match ($this) {
            self::SELESAI => 'Selesai',
            self::GAGAL => 'Gagal',
            self::MENUNGGU => 'Menunggu',
            self::DIPROSES => 'Diproses',
        };
    }
    public function getColor(): string|array|null
    {
        return match ($this) {
            self::SELESAI => 'success',
            self::DIPROSES => 'primary',
            self::GAGAL => 'danger',
            self::MENUNGGU => 'warning',
        };
    }
    public function getIcon(): ?string
    {
        return match ($this) {
            self::SELESAI => 'heroicon-m-check-circle',
            self::MENUNGGU => 'heroicon-m-clock',
            self::GAGAL => 'heroicon-m-x-circle',
            self::DIPROSES => 'heroicon-m-arrow-path',
        };
    }
}
