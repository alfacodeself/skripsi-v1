<?php

namespace App\Models;

use App\Enums\InvoiceStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tagihan extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'tagihan';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id_langganan',
        'total_tagihan',
        'sisa_tagihan',
        'jatuh_tempo',
        'status_angsuran',
        'jumlah_angsuran',
        'status',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'id_langganan' => 'integer',
        'jatuh_tempo' => 'date',
        'status_angsuran' => 'boolean',
        'status' => InvoiceStatus::class
    ];

    public function detailTagihan(): HasMany
    {
        return $this->hasMany(DetailTagihan::class, 'id_tagihan');
    }

    public function transaksiTagihan(): HasMany
    {
        return $this->hasMany(TransaksiTagihan::class, 'id_tagihan');
    }

    public function langganan(): BelongsTo
    {
        return $this->belongsTo(Langganan::class, 'id_langganan');
    }
}
