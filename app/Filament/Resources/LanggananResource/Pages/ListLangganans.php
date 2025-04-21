<?php

namespace App\Filament\Resources\LanggananResource\Pages;

use App\Enums\InvoiceStatus;
use App\Enums\SubscriptionStatus;
use App\Filament\Resources\LanggananResource;
use App\Models\Langganan;
use Carbon\Carbon;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;

class ListLangganans extends ListRecords
{
    protected static string $resource = LanggananResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->label('Buat Langganan Baru'),
        ];
    }

    public function getTabs(): array
    {
        return [
            'semua' => Tab::make('Semua')
                ->icon('heroicon-m-bars-3'),

            'diperiksa' => Tab::make('Diperiksa')
                ->icon('heroicon-m-clock')
                ->badge(Langganan::query()->whereStatus(SubscriptionStatus::DIPERIKSA)->count())
                ->badgeColor('gray')
                ->modifyQueryUsing(fn($query) => $query->where('status', SubscriptionStatus::DIPERIKSA)),

            'disetujui' => Tab::make('Disetujui')
                ->icon('heroicon-m-check-circle')
                ->badgeColor('success')
                ->modifyQueryUsing(fn($query) => $query->where('status', SubscriptionStatus::DISETUJUI)),

            'ditolak' => Tab::make('Ditolak')
                ->icon('heroicon-m-x-circle')
                ->badgeColor('danger')
                ->modifyQueryUsing(fn($query) => $query->where('status', SubscriptionStatus::DITOLAK)),

            'aktif' => Tab::make('Aktif')
                ->icon('heroicon-m-bolt')
                ->badgeColor('success')
                ->modifyQueryUsing(fn($query) => $query->where('status', SubscriptionStatus::AKTIF)),

            'nonaktif' => Tab::make('Nonaktif')
                ->icon('heroicon-m-pause-circle')
                ->badgeColor('warning')
                ->modifyQueryUsing(fn($query) => $query->where('status', SubscriptionStatus::NONAKTIF)),

            'kadaluarsa' => Tab::make('Kadaluarsa')
                ->icon('heroicon-m-calendar-date-range')
                ->badge(Langganan::query()->whereDate('tanggal_kadaluarsa', '<', Carbon::now())->count())
                ->badgeColor('danger')
                ->modifyQueryUsing(fn($query) => $query->whereDate('tanggal_kadaluarsa', '<', Carbon::now())),

            'belum_lunas' => Tab::make('Tagihan')
                ->icon('heroicon-m-currency-dollar')
                ->badge(Langganan::query()->with('tagihan')->whereHas('tagihan', fn($q) => $q->where('status', InvoiceStatus::BELUM_LUNAS))->count())
                ->badgeColor('warning')
                ->modifyQueryUsing(fn($query) => $query->whereHas('tagihan', fn($q) => $q->where('status', InvoiceStatus::BELUM_LUNAS))),
        ];
    }
}
