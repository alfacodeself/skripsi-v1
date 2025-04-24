<?php

namespace App\Livewire\Widget\Langganan;

use App\Models\Langganan;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class TagihanWidget extends BaseWidget
{
    public ?Langganan $langganan = null;

    public function mount(Langganan $langganan): void
    {
        $this->langganan = $langganan;
    }
    protected function getStats(): array
    {
        $totalTagihan = $this->langganan->tagihan()->sum('total_tagihan');
        $sisaTagihan = $this->langganan->tagihan()->sum('sisa_tagihan');
        $tagihanTerbayar = $totalTagihan - $sisaTagihan;
        return [
            Stat::make('Total Tagihan', 'Rp. ' . number_format($totalTagihan))
                ->description('Semua tagihan langganan')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('gray'),

            Stat::make('Tagihan Terbayar', 'Rp. ' . number_format($tagihanTerbayar))
                ->description('Tagihan yang sudah terbayar')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),

            Stat::make('Sisa Tagihan', 'Rp. ' . number_format($sisaTagihan))
                ->description('Sisa tagihan belum dibayarkan')
                ->descriptionIcon('heroicon-m-exclamation-circle')
                ->color('danger'),
        ];
    }
}
