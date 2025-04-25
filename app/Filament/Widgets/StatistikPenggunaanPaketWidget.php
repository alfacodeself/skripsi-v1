<?php

namespace App\Filament\Widgets;

use App\Models\Langganan;
use App\Models\Paket;
use Filament\Widgets\ChartWidget;

class StatistikPenggunaanPaketWidget extends ChartWidget
{
    protected static ?string $heading = 'Statistik Penggunaan Paket Per Tahun';
    protected int | string | array $columnSpan = 'full';

    public ?string $filter = null;

    protected function getFilters(): ?array
    {
        return Langganan::selectRaw('YEAR(tanggal_aktif) as year')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year', 'year') // ['2024' => '2024', dst]
            ->toArray();
    }

    protected function getData(): array
    {
        $year = $this->filter ?? now()->year;
        // Ambil semua paket
        $pakets = Paket::with('kategori')->get();
        $labels = [
            'Januari',
            'Februari',
            'Maret',
            'April',
            'Mei',
            'Juni',
            'Juli',
            'Agustus',
            'September',
            'Oktober',
            'November',
            'Desember',
        ];

        $datasets = [];

        foreach ($pakets as $paket) {
            $dataPerBulan = [];

            foreach (range(1, 12) as $month) {
                $jumlah = Langganan::where('id_paket', $paket->id)
                    ->whereYear('tanggal_aktif', $year)
                    ->whereMonth('created_at', $month)
                    ->count();

                $dataPerBulan[] = $jumlah;
            }

            $datasets[] = [
                'label' => "{$paket->nama} - {$paket->kategori->nama}",
                'data' => $dataPerBulan,
                'backgroundColor' => $paket->kategori->warna,
            ];
        }

        return [
            'labels' => $labels,
            'datasets' => $datasets,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
