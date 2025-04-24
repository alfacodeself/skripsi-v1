<?php

namespace App\Models;

use App\Enums\ReportStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class Pengaduan extends Model
{
    use HasFactory;

    protected $table = 'pengaduan';

    protected $guarded = [];

    protected $casts = [
        'status' => ReportStatus::class
    ];

    protected static function booted()
    {
        static::saving(function (self $pengaduan) {
            if ($pengaduan->isDirty('lampiran') && $pengaduan->getOriginal('lampiran') != null) {
                Storage::disk('public')->delete($pengaduan->getOriginal('lampiran'));
            }
        });
        static::deleted(function (self $pengaduan) {
            if ($pengaduan->lampiran != null) {
                Storage::disk('public')->delete($pengaduan->lampiran);
            }
        });
    }

    public function kategoriPengaduan(): BelongsTo
    {
        return $this->belongsTo(KategoriPengaduan::class, 'id_kategori_pengaduan');
    }
    public function langganan(): BelongsTo
    {
        return $this->belongsTo(Langganan::class, 'id_langganan');
    }
    public function admin(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'ditangani_oleh');
    }
}
