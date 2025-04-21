<?php

namespace App\Models;

use App\Enums\DefaultStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Paket extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'paket';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    public function getRouteKeyName()
    {
        return 'slug';
    }

    protected $fillable = [
        'id_kategori',
        'nama',
        'slug',
        'deskripsi',
        'harga',
        'layanan',
        'bisa_diangsur',
        'maksimal_angsuran',
        'minimal_jumlah_angsuran',
        'durasi_hari_angsuran',
        'status',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'id_kategori' => 'integer',
        'layanan' => 'array',
        'bisa_diangsur' => 'boolean',
        'status' => DefaultStatus::class
    ];

    public function biayaPaket(): HasMany
    {
        return $this->hasMany(BiayaPaket::class, 'id_paket');
    }

    public function langganan(): HasMany
    {
        return $this->hasMany(Langganan::class, 'id_paket');
    }

    public function kategori(): BelongsTo
    {
        return $this->belongsTo(Kategori::class, 'id_kategori', 'id');
    }
}
