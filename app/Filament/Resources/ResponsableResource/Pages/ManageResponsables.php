<?php

namespace App\Filament\Resources\ResponsableResource\Pages;

use App\Filament\Resources\ResponsableResource;
use App\Models\User;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageResponsables extends ManageRecords
{
    protected static string $resource = ResponsableResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->after(function (User $record) {
                    $record->assignRole(['responsable']);

                    $record->update([
                        'datas' => [
                            'tel' => $record->datas['tel'],
                            'visible' => true
                        ],
                    ]);
                }),
        ];
    }
}
