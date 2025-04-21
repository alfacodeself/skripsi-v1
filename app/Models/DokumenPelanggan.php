<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class DokumenPelanggan extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'dokumen_pelanggan';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id_dokumen',
        'id_pelanggan',
        'path',
        'status',
        'akses_pelanggan',
        'catatan',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'id_dokumen' => 'integer',
        'id_pelanggan' => 'integer',
        'akses_pelanggan' => 'boolean',
    ];

    protected static function booted()
    {
        static::saving(function (self $dokumenPelanggan) {
            if ($dokumenPelanggan->isDirty('path') && $dokumenPelanggan->getOriginal('path') != null) {
                Storage::disk('public')->delete($dokumenPelanggan->getOriginal('path'));
            }
        });
        static::deleted(function (self $dokumenPelanggan) {
            if ($dokumenPelanggan->path != null) {
                Storage::disk('public')->delete($dokumenPelanggan->path);
            }
        });
    }

    public function jenisDokumen(): BelongsTo
    {
        return $this->belongsTo(JenisDokumen::class, 'id_dokumen');
    }

    public function pelanggan(): BelongsTo
    {
        return $this->belongsTo(Pelanggan::class, 'id_pelanggan');
    }
}
