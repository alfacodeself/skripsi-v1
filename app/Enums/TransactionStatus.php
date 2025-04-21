<?php

namespace App\Enums;

enum TransactionStatus: string
{
  case WAITING = 'menunggu';
  case PENDING = 'diperiksa';
  case CANCELLED = 'dibatalkan';
  case EXPIRED = 'kadaluarsa';
  case FAILED = 'gagal';
  case PAID = 'lunas';
}
