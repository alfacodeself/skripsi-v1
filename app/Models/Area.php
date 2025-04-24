<?php

namespace App\Models;

use App\Enums\DefaultStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Area extends Model
{
    use HasFactory;

    protected $table = 'area';

    protected $fillable = [
        'latitude',
        'longitude',
        'provinsi',
        'kabupaten',
        'kecamatan',
        'kode_pos',
        'alamat_lengkap',
        'status',
    ];

    protected $casts = [
        'status' => DefaultStatus::class
    ];
}
