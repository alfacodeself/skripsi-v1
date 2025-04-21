<?php

namespace App\Enums;

use Filament\Support\Contracts\{HasColor, HasIcon, HasLabel};

enum DefaultStatus: string implements HasLabel, HasColor, HasIcon
{
    case AKTIF = 'aktif';
    case NONAKTIF = 'tidak aktif';

    public function getLabel(): ?string
    {
        // return $this->name;
        return match ($this) {
            self::AKTIF => 'Aktif',
            self::NONAKTIF => 'Tidak Aktif',
        };
    }
    public function getColor(): string|array|null
    {
        return match ($this) {
            self::AKTIF => 'success',
            self::NONAKTIF => 'danger',
        };
    }
    public function getIcon(): ?string
    {
        return match ($this) {
            self::AKTIF => 'heroicon-m-check-circle',
            self::NONAKTIF => 'heroicon-m-x-circle',
        };
    }
}
