<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum UserStatus: string implements HasLabel, HasColor, HasIcon
{
  case PENDING = 'diperiksa';
  case WARNING = 'peringatan';
  case BLOCKED = 'diblokir';
  case ACTIVE = 'aktif';
  case INACTIVE = 'tidak aktif';

  public function getLabel(): ?string
    {
        return match ($this) {
            self::ACTIVE => 'Aktif',
            self::INACTIVE => 'Tidak Aktif',
            self::PENDING => 'Diperiksa',
            self::WARNING => 'Peringatan',
            self::BLOCKED => 'Diblokir',
        };
    }
    public function getColor(): string|array|null
    {
        return match ($this) {
            self::ACTIVE => 'success',
            self::INACTIVE => 'gray',
            self::PENDING => 'warning',
            self::WARNING => 'danger',
            self::BLOCKED => 'danger',
        };
    }
    public function getIcon(): ?string
    {
        return match ($this) {
            self::ACTIVE => 'heroicon-m-check-circle',
            self::INACTIVE => 'heroicon-m-x-circle',
            self::PENDING => 'heroicon-m-clock',
            self::WARNING => 'heroicon-m-exclamation-triangle',
            self::BLOCKED => 'heroicon-m-no-symbol',
        };
    }
}
