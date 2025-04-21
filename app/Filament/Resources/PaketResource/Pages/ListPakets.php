<?php

namespace App\Filament\Resources\PaketResource\Pages;

use App\Filament\Resources\PaketResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Contracts\Support\Htmlable;

class ListPakets extends ListRecords
{
    protected static string $resource = PaketResource::class;

    public function getTitle(): string|Htmlable
    {
        return 'Data Paket';
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->label('Buat Paket Baru'),
        ];
    }
}
