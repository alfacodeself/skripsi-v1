<?php

namespace App\Models;

use App\Enums\CustomerStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class Pelanggan extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'pelanggan';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'foto',
        'nama',
        'kode',
        'email',
        'telepon',
        'password',
        'status',
        'catatan',
        'tanggal_verifikasi_email',
        'tanggal_verifikasi_telepon'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'tanggal_verifikasi_email' => 'date',
        'tanggal_verifikasi_telepon' => 'date',
        'status' => CustomerStatus::class
    ];

    public function getRouteKeyName()
    {
        return 'kode';
    }

    protected static function booted()
    {
        static::saving(function (self $pelanggan) {
            if ($pelanggan->isDirty('foto') && $pelanggan->getOriginal('foto') != null) {
                Storage::disk('public')->delete($pelanggan->getOriginal('foto'));
            }
        });
        static::deleted(function (self $pelanggan) {
            if ($pelanggan->foto != null) {
                Storage::disk('public')->delete($pelanggan->foto);
            }
        });
    }

    public function langganan(): HasMany
    {
        return $this->hasMany(Langganan::class, 'id_pelanggan');
    }
    public function dokumenPelanggan(): HasMany
    {
        return $this->hasMany(DokumenPelanggan::class, 'id_pelanggan');
    }

    public function getFilamentAvatarUrl(): ?string
    {
        return $this->getDefaultAvatar();
    }

    public function getDefaultAvatar(): ?string
    {
        return asset('assets/img/default-avatar.jpg');
    }
}
