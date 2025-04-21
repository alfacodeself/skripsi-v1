<?php

namespace App\Models;

use App\Enums\SubscriptionProgressStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class ProgressLangganan extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'progress_langganan';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id_progress',
        'id_langganan',
        'status',
        'bukti',
        'keterangan',
        'tanggal_perencanaan',
    ];

    protected static function booted()
    {
        static::saving(function (self $progressLangganan) {
            if ($progressLangganan->isDirty('bukti') && $progressLangganan->getOriginal('bukti') != null) {
                Storage::disk('public')->delete($progressLangganan->getOriginal('bukti'));
            }
            if (!$progressLangganan->id_langganan || !$progressLangganan->id_progress) {
                return;
            }

            $exists = static::where('id_langganan', $progressLangganan->id_langganan)
                ->where('id_progress', $progressLangganan->id_progress)
                ->where('id', '!=', $progressLangganan->id)
                ->exists();

            if ($exists) {
                throw new \Exception('Progress ini sudah ada dalam langganan.');
            }
        });
        static::deleted(function (self $progressLangganan) {
            if ($progressLangganan->bukti != null) {
                Storage::disk('public')->delete($progressLangganan->bukti);
            }
        });
    }

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'id_progress' => 'integer',
        'id_langganan' => 'integer',
        'tanggal_perencanaan' => 'date',
        'status' => SubscriptionProgressStatus::class
    ];

    public function progress(): BelongsTo
    {
        return $this->belongsTo(Progress::class, 'id_progress');
    }

    public function langganan(): BelongsTo
    {
        return $this->belongsTo(Langganan::class, 'id_langganan');
    }
}
