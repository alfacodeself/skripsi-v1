<?php

namespace App\Filament\Resources\PelangganResource\Pages;

use App\Filament\Resources\PelangganResource;
use App\Models\JenisDokumen;
use Carbon\Carbon;
use Filament\Actions;
use Filament\Forms\Components\{FileUpload, Repeater, RichEditor, Section, Select, TextInput, Toggle};
use Filament\Forms\Components\Wizard\Step;
use Filament\Forms\{Get, Set};
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Builder;

class CreatePelanggan extends CreateRecord
{

    protected static string $resource = PelangganResource::class;

    public function getTitle(): string|Htmlable
    {
        return 'Pelanggan Baru';
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
            ->body('Pelanggan berhasil dibuat.');
    }
}
