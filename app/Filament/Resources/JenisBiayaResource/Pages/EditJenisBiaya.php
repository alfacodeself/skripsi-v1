<?php

namespace App\Filament\Resources\JenisBiayaResource\Pages;

use App\Filament\Resources\JenisBiayaResource;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Contracts\Support\Htmlable;

class EditJenisBiaya extends EditRecord
{
    protected static string $resource = JenisBiayaResource::class;

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
            ->body('Jenis biaya berhasil diubah.');
    }
    public function getTitle(): string|Htmlable
    {
        return 'Ubah Jenis Biaya';
    }
}
