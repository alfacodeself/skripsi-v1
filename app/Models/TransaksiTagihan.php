<?php

namespace App\Models;

use App\Enums\TransactionStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TransaksiTagihan extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'transaksi_tagihan';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id_tagihan',
        'kode',
        'jumlah_bayar',
        'metode_pembayaran',
        'bukti_pembayaran',
        'midtrans_order_id',
        'midtrans_response',
        'snap_token',
        'keterangan',
        'tanggal_lunas',
        'tanggal_kadaluarsa',
        'status',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'id_tagihan' => 'integer',
        'status' => TransactionStatus::class
    ];

    public function tagihan(): BelongsTo
    {
        return $this->belongsTo(Tagihan::class, 'id_tagihan');
    }
}
