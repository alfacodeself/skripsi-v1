<?php

namespace App\Filament\Imports;

use App\Models\Kategori;
use Carbon\Carbon;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Support\Str;

class KategoriImporter extends Importer
{
    protected static ?string $model = Kategori::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('nama')
                ->requiredMapping()
                ->rules(['required', 'max:255'])
                ->examples('Kategori #3'),
            ImportColumn::make('deskripsi')
                ->example('Deskripsi kategori #3'),
            ImportColumn::make('warna')
                ->rules(['max:100'])
                ->example('#00fa64'),
            ImportColumn::make('jumlah_hari')
                ->requiredMapping()
                ->numeric()
                ->rules(['required', 'integer'])
                ->example('90'),
            ImportColumn::make('status')
                ->requiredMapping()
                ->rules(['required'])
                ->example('aktif'),
        ];
    }

    public function resolveRecord(): ?Kategori
    {
        // return Kategori::firstOrNew([
        //     // Update existing records, matching them by `$this->data['column_name']`
        //     'email' => $this->data['email'],
        // ]);

        return new Kategori(['slug' => Str::slug($this->data['nama']) . '-' . Carbon::now()->format('Y-m-d-His')]);
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your kategori import has completed and ' . number_format($import->successful_rows) . ' ' . str('row')->plural($import->successful_rows) . ' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to import.';
        }

        return $body;
    }
}
