<?php

namespace App\Models;

use App\Enums\UserStatus;
use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasAvatar;
use Filament\Models\Contracts\HasName;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;

class Admin extends Authenticatable implements FilamentUser, HasName, HasAvatar
{
    use HasFactory, Notifiable;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'admin';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'foto',
        'nama',
        'email',
        'password',
        'superadmin',
        'status',
        'catatan',
        'tanggal_verifikasi_email'
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
        'status' => UserStatus::class,
        'superadmin' => 'boolean'
    ];

    protected static function booted()
    {
        static::saving(function (self $admin) {
            if ($admin->isDirty('foto') && $admin->getOriginal('foto') != null) {
                Storage::disk('public')->delete($admin->getOriginal('foto'));
            }
        });
        static::deleted(function (self $admin) {
            if ($admin->foto != null) {
                Storage::disk('public')->delete($admin->foto);
            }
        });
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return true;
    }

    public function getFilamentName(): string
    {
        return $this->nama;
    }

    public function getFilamentAvatarUrl(): ?string
    {
        if ($this->foto && Storage::disk('public')->exists($this->foto)) {
            return Storage::url($this->foto); // Balikin URL foto-nya
        }

        // Fallback ke avatar default
        return $this->getDefaultAvatar();
    }

    public function getDefaultAvatar(): ?string
    {
        return asset('assets/img/default-avatar.jpg');
    }
}
