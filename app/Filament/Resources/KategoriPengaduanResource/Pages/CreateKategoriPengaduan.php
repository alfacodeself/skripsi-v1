<?php

namespace App\Filament\Resources\KategoriPengaduanResource\Pages;

use App\Filament\Resources\KategoriPengaduanResource;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Contracts\Support\Htmlable;

class CreateKategoriPengaduan extends CreateRecord
{
    protected static string $resource = KategoriPengaduanResource::class;
    public function getTitle(): string|Htmlable
    {
        return 'Kategori Pengaduan Baru';
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Berhasil!')
            ->body('Kategori berhasil dibuat.');
    }
}
