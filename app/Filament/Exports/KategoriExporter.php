<?php

namespace App\Filament\Exports;

use App\Models\Kategori;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class KategoriExporter extends Exporter
{
    protected static ?string $model = Kategori::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('nama')->label('Nama Kategori'),
            ExportColumn::make('deskripsi')->label('Deskripsi Kategori'),
            ExportColumn::make('jumlah_hari')->label('Total Hari'),
            ExportColumn::make('warna')->label('Warna'),
            ExportColumn::make('status')->label('Status Kategori'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your kategori export has completed and ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}
