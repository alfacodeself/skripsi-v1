<?php

namespace App\Models;

use App\Enums\DefaultStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KategoriPengaduan extends Model
{
    use HasFactory;

    protected $table = 'kategori_pengaduan';

    protected $guarded = [];

    protected $casts = [
        'status' => DefaultStatus::class
    ];

    public function pengaduan(): HasMany
    {
        return $this->hasMany(Pengaduan::class, 'id_kategpri_pengaduan');
    }
}
