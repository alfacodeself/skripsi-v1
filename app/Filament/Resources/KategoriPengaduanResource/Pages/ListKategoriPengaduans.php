<?php

namespace App\Filament\Resources\KategoriPengaduanResource\Pages;

use App\Filament\Resources\KategoriPengaduanResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Contracts\Support\Htmlable;

class ListKategoriPengaduans extends ListRecords
{
    protected static string $resource = KategoriPengaduanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Buat Kategori Pengaduan Baru')
                ->modal(),
        ];
    }

    public function getTitle(): string|Htmlable
    {
        return 'Data Kategori Pengaduan';
    }
}
