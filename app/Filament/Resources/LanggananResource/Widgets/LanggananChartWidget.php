<?php

namespace App\Filament\Resources\LanggananResource\Widgets;

use App\Enums\DefaultStatus;
use App\Enums\InvoiceStatus;
use App\Enums\SubscriptionStatus;
use App\Filament\Resources\LanggananResource\Pages\ListLangganans;
use App\Models\Langganan;
use App\Models\Paket;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Pages\Dashboard\Concerns\HasFiltersForm;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\Concerns\InteractsWithPageTable;
use Illuminate\Database\Eloquent\Builder;

class LanggananChartWidget extends ChartWidget
{
    use InteractsWithPageFilters, InteractsWithPageTable;

    protected static ?string $heading = "Statistik Langganan Tahunan ";

    protected int|string|array $columnSpan = 2;

    protected static ?string $maxHeight = '200px';

    public ?string $filter = null;

    protected function getTablePage(): string
    {
        return ListLangganans::class;
    }

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
                $jumlah = $this->getPageTableQuery()->where('id_paket', $paket->id)
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
