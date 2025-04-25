<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\AlamatLanggananWidget;
use App\Filament\Widgets\LanggananWidget;
use App\Filament\Widgets\PaketUsageChart;
use App\Filament\Widgets\StatistikPenggunaanPaketWidget;
use App\Filament\Widgets\TagihanWidget;
use App\Filament\Widgets\WelcomeWidget;
use Filament\Pages\Dashboard as PagesDashboard;

class Dashboard extends PagesDashboard
{
    protected static ?string $navigationIcon = 'heroicon-o-home';
    protected static ?string $title = 'Dashboard';

    // Optional: taruh di atas menu
    protected static ?int $navigationSort = -1;

    protected static string $view = 'filament.pages.dashboard';

    protected function getHeaderWidgets(): array
    {
        return [
            WelcomeWidget::class,
            TagihanWidget::class,
            StatistikPenggunaanPaketWidget::class,
            LanggananWidget::class,
            AlamatLanggananWidget::class
        ];
    }
}
