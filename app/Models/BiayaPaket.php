<?php

namespace App\Models;

use App\Enums\DefaultStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

class BiayaPaket extends Pivot
{
    use HasFactory;
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'biaya_paket';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id_paket',
        'id_jenis_biaya',
        'besar_biaya',
        'status',
        'keterangan',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'id_paket' => 'integer',
        'id_jenis_biaya' => 'integer',
        'status' => DefaultStatus::class
    ];

    public function paket(): BelongsTo
    {
        return $this->belongsTo(Paket::class, 'id_paket');
    }
    public function jenisBiaya(): BelongsTo
    {
        return $this->belongsTo(JenisBiaya::class, 'id_jenis_biaya');
    }
}
