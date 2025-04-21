<?php

namespace App\Enums;

use Filament\Support\Contracts\{HasColor, HasIcon, HasLabel};

enum CustomerDocumentStatus: string implements HasLabel, HasColor, HasIcon
{
    case DIPERIKSA = 'diperiksa';
    case DISETUJUI = 'disetujui';
    case DITOLAK = 'ditolak';

    public function getLabel(): ?string
    {
        // return $this->name;
        return match ($this) {
            self::DISETUJUI => 'Disetujui',
            self::DITOLAK => 'Ditolak',
            self::DIPERIKSA => 'Diperiksa',
        };
    }
    public function getColor(): string|array|null
    {
        return match ($this) {
            self::DISETUJUI => 'success',
            self::DIPERIKSA => 'warning',
            self::DITOLAK => 'danger',
        };
    }
    public function getIcon(): ?string
    {
        return match ($this) {
            self::DISETUJUI => 'heroicon-m-check-circle',
            self::DIPERIKSA => 'heroicon-m-clock',
            self::DITOLAK => 'heroicon-m-x-circle',
        };
    }
}
