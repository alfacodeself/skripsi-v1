<?php

namespace App\Models;

use App\Enums\DefaultStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class JenisDokumen extends Model
{
    use HasFactory;
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'jenis_dokumen';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'nama_dokumen',
        'path_dokumen',
        'contoh_dokumen',
        'deskripsi_dokumen',
        'status',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'status' => DefaultStatus::class
    ];

    protected static function booted()
    {
        static::saving(function (self $jenisDokumen) {
            if ($jenisDokumen->isDirty('contoh_dokumen') && $jenisDokumen->getOriginal('contoh_dokumen') != null) {
                Storage::disk('public')->delete($jenisDokumen->getOriginal('contoh_dokumen'));
            }
        });
        static::deleted(function (self $jenisDokumen) {
            if ($jenisDokumen->contoh_dokumen != null) {
                Storage::disk('public')->delete($jenisDokumen->contoh_dokumen);
            }
        });
    }

    public function dokumenPelanggan(): HasMany
    {
        return $this->hasMany(DokumenPelanggan::class, 'id_dokumen');
    }
}
