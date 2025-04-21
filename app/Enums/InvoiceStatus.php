<?php

namespace App\Enums;

use Filament\Support\Contracts\{HasColor, HasIcon, HasLabel};

enum InvoiceStatus: string implements HasLabel, HasColor, HasIcon
{
  case LUNAS = 'lunas';
  case BELUM_LUNAS = 'belum lunas';
  case DALAM_ANGSURAN = 'dalam angsuran';

  public function getLabel(): ?string
  {
    // return $this->name;
    return match ($this) {
      self::LUNAS => 'Lunas',
      self::BELUM_LUNAS => 'Tidak Lunas',
      self::DALAM_ANGSURAN => 'Dalam Angsuran',
    };
  }
  public function getColor(): string|array|null
  {
    return match ($this) {
      self::LUNAS => 'primary',
      self::BELUM_LUNAS => 'danger',
      self::DALAM_ANGSURAN => 'warning',
    };
  }
  public function getIcon(): ?string
  {
    return match ($this) {
      self::LUNAS => 'heroicon-m-check-circle',
      self::BELUM_LUNAS => 'heroicon-m-x-circle',
      self::DALAM_ANGSURAN => 'heroicon-m-clock'
    };
  }
}
