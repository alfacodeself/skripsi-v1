<?php

namespace App\Filament\Resources\LandingpageResource\Pages;

use App\Filament\Resources\LandingpageResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListLandingpages extends ListRecords
{
    protected static string $resource = LandingpageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
