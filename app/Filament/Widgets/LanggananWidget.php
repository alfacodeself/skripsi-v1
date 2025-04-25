<?php

namespace App\Filament\Widgets;

use App\Enums\InvoiceStatus;
use App\Enums\ReportStatus;
use App\Enums\SubscriptionStatus;
use App\Filament\Resources\LanggananResource;
use App\Filament\Resources\PengaduanResource;
use App\Models\Langganan;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class LanggananWidget extends BaseWidget
{
    protected static ?int $sort = 5;

    protected function getStats(): array
    {
        $langganan = Langganan::query();
        $langgananPending = $langganan->whereStatus(SubscriptionStatus::DIPERIKSA)->count();
        $langgananExpired = $langganan->whereDate('tanggal_kadaluarsa', '<', Carbon::now())->count();
        $langgananHasTagihan = $langganan->whereHas('tagihan', fn($q) => $q->where('status', InvoiceStatus::BELUM_LUNAS))->count();
        $langgananHasPengaduan = $langganan->whereHas('pengaduan', fn($q) => $q->whereIn('status', [ReportStatus::PENDING, ReportStatus::WAITING]))->count();
        return [
            Stat::make('Pengajuan Langganan', $langgananPending)
                ->description('Perlu diverifikasi')
                ->url(LanggananResource::getUrl('index'))
                ->descriptionIcon('heroicon-m-clock')
                ->color('success'),

            Stat::make('Langganan Kadaluarsa', $langgananExpired)
                ->description('Terlewat masa aktif')
                ->url(LanggananResource::getUrl('index'))
                ->descriptionIcon('heroicon-m-calendar-date-range')
                ->color('danger'),

            Stat::make('Langganan Bertagihan', $langgananHasTagihan)
                ->description('Belum lunas')
                ->url(LanggananResource::getUrl('index'))
                ->descriptionIcon('heroicon-m-currency-dollar')
                ->color('warning'),

            Stat::make('Pengaduan Langganan', $langgananHasPengaduan)
                ->description('Pengaduan masalah')
                ->url(PengaduanResource::getUrl('index'))
                ->descriptionIcon('heroicon-o-shield-exclamation')
                ->color('danger'),

        ];
    }
}
