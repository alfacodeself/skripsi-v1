<?php

namespace App\Enums;

enum PaymentMethod: string
{
  case MIDTRANS = 'midtrans';
  case CASH = 'cash';
}
