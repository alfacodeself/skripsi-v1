<?php

namespace App\Filament\Resources\JenisBiayaResource\Pages;

use App\Filament\Resources\JenisBiayaResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Contracts\Support\Htmlable;

class ListJenisBiayas extends ListRecords
{
    protected static string $resource = JenisBiayaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->label('Buat Jenis Biaya Baru'),
        ];
    }
    public function getTitle(): string|Htmlable
    {
        return 'Data Jenis Biaya';
    }
}
