<?php

namespace App\Filament\Resources\JenisDokumenResource\Pages;

use App\Filament\Resources\JenisDokumenResource;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Contracts\Support\Htmlable;

class CreateJenisDokumen extends CreateRecord
{
    protected static string $resource = JenisDokumenResource::class;
    public function getTitle(): string|Htmlable
    {
        return 'Jenis Dokumen Baru';
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
            ->body('Jenis dokumen berhasil dibuat.');
    }
}
