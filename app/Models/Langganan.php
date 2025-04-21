<?php

namespace App\Models;

use App\Enums\SubscriptionStatus;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Langganan extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'langganan';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id_paket',
        'id_pelanggan',
        'kode_layanan',
        'latitude',
        'longitude',
        'alamat_lengkap',
        'status',
        'catatan',
        'tanggal_pemasangan',
        'tanggal_aktif',
        'tanggal_kadaluarsa',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'id_paket' => 'integer',
        'id_pelanggan' => 'integer',
        'latitude' => 'double',
        'longitude' => 'double',
        'tanggal_pemasangan' => 'date',
        'tanggal_aktif' => 'date',
        'tanggal_kadaluarsa' => 'date',
        'status' => SubscriptionStatus::class
    ];

    public function getRouteKeyName()
    {
        return 'kode_layanan';
    }

    protected static function booted()
    {
        // Cek progress
        $firstProgress = Progress::where('urutan', 1)->where('status', 'aktif')->first();
        static::creating(function (self $langganan) use ($firstProgress) {
            if (!$firstProgress) throw new Exception('Progress dengan urutan 1 harus dibuat.');
            // Cek apakah ada tanggal aktif dan tanggal kadaluarsa

            $status = SubscriptionStatus::DIPERIKSA;
            if ($langganan->tanggal_aktif != null || $langganan->tanggal_kadaluarsa != null) {
                if (Carbon::now()->greaterThan($langganan->tanggal_kadaluarsa)) {
                    $status = SubscriptionStatus::NONAKTIF;
                } else {
                    $status = SubscriptionStatus::AKTIF;
                }
            }
            $langganan->status = $status;
            $langganan->kode_layanan = Str::upper(Str::random(11)) . Carbon::now()->format('YmdHis');
        });
        // static::created(function (self $langganan) use ($firstProgress) {
        //     if ($langganan->tanggal_aktif == null || $langganan->tanggal_kadaluarsa == null) {
        //         // Buat progress pertama selesai
        //         $langganan->progressLangganan()->create([
        //             'id_progress' => $firstProgress->id,
        //             'status' => 'selesai',
        //             'keterangan' => $firstProgress->nama . ' Selesai.',
        //             'tanggal_perencanaan' => Carbon::now(),
        //         ]);
        //         // Jika terdapat progress selanjutnya, buat progress dengan status menunggu
        //         $nextProgress = Progress::where('urutan', $firstProgress->urutan + 1)->where('status', 'aktif')->first();
        //         if ($nextProgress) {
        //             $langganan->progressLangganan()->create([
        //                 'id_progress' => $nextProgress->id,
        //                 'status' => 'menunggu',
        //                 'keterangan' => 'Menunggu ' . $nextProgress->nama . '.',
        //                 'tanggal_perencanaan' => Carbon::now()->addDay(),
        //             ]);
        //         }
        //     }
        // });
    }

    public function progressLangganan(): HasMany
    {
        return $this->hasMany(ProgressLangganan::class, 'id_langganan');
    }

    public function tagihan(): HasMany
    {
        return $this->hasMany(Tagihan::class, 'id_langganan');
    }

    public function paket(): BelongsTo
    {
        return $this->belongsTo(Paket::class, 'id_paket');
    }

    public function pelanggan(): BelongsTo
    {
        return $this->belongsTo(Pelanggan::class, 'id_pelanggan');
    }
}
