<?php

namespace App\Filament\Resources\OrphanageResource\Pages;

use App\Filament\Resources\OrphanageResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditOrphanage extends EditRecord
{
    protected static string $resource = OrphanageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
