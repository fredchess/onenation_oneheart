<?php

namespace App\Filament\Resources\VersementResource\Pages;

use App\Filament\Resources\VersementResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListVersements extends ListRecords
{
    protected static string $resource = VersementResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
