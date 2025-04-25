<?php

namespace App\Filament\Resources\LandingpageResource\Pages;

use App\Filament\Resources\LandingpageResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditLandingpage extends EditRecord
{
    protected static string $resource = LandingpageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
