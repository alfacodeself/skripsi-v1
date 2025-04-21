<?php

namespace App\Models;

use App\Enums\DefaultStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class JenisBiaya extends Model
{
    use HasFactory;
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'jenis_biaya';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'nama',
        'jenis_biaya',
        'berulang',
        'status',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'berulang' => 'boolean',
        'status' => DefaultStatus::class
    ];

    // public function paket(): BelongsToMany
    // {
    //     return $this->belongsToMany(Paket::class, 'biaya_paket', 'id_jenis_biaya', 'id_paket')
    //         ->withPivot(['besar_biaya', 'status', 'keterangan'])
    //         ->using(BiayaPaket::class);
    // }
    public function biayaPaket(): HasMany
    {
        return $this->hasMany(BiayaPaket::class, 'id_jenis_biaya');
    }
}
