<?php

namespace App\Filament\Resources\KategoriPengaduanResource\Pages;

use App\Filament\Resources\KategoriPengaduanResource;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Contracts\Support\Htmlable;

class EditKategoriPengaduan extends EditRecord
{
    protected static string $resource = KategoriPengaduanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
    protected function getSavedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Berhasil!')
            ->body('Kategori berhasil diubah.');
    }
    public function getTitle(): string|Htmlable
    {
        return 'Ubah Kategori Pengaduan';
    }
}
