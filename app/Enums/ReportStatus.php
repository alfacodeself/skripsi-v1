<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum ReportStatus: string implements HasLabel, HasColor, HasIcon
{
  case WAITING = 'menunggu';
  case PENDING = 'diproses';
  case REJECTED = 'ditolak';
  case FINISHED = 'selesai';

  public function getLabel(): ?string
    {
        // return $this->name;
        return match ($this) {
            self::FINISHED => 'Selesai',
            self::REJECTED => 'Ditolak',
            self::WAITING => 'Menunggu',
            self::PENDING => 'Diproses',
        };
    }
    public function getColor(): string|array|null
    {
        return match ($this) {
            self::FINISHED => 'success',
            self::PENDING => 'primary',
            self::REJECTED => 'danger',
            self::WAITING => 'warning',
        };
    }
    public function getIcon(): ?string
    {
        return match ($this) {
            self::FINISHED => 'heroicon-m-check-circle',
            self::WAITING => 'heroicon-m-clock',
            self::REJECTED => 'heroicon-m-x-circle',
            self::PENDING => 'heroicon-m-arrow-path',
        };
    }
}
