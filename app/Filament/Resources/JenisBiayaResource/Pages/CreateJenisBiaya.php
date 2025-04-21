<?php

namespace App\Filament\Resources\JenisBiayaResource\Pages;

use App\Filament\Resources\JenisBiayaResource;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Contracts\Support\Htmlable;

class CreateJenisBiaya extends CreateRecord
{
    protected static string $resource = JenisBiayaResource::class;
    public function getTitle(): string|Htmlable
    {
        return 'Jenis Biaya Baru';
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
            ->body('Jenis biaya berhasil dibuat.');
    }
}
