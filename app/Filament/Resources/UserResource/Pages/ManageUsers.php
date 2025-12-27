<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Enums\UserRoleEnum;
use App\Filament\Resources\UserResource;
use App\Models\User;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;
use Spatie\Permission\Models\Role;

class ManageUsers extends ManageRecords
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->after(function (User $record) {
                    // if (Role::where('name', UserRoleEnum::RESPONSABLE->value)->first() == null) Role::create(['name' => UserRoleEnum::RESPONSABLE->value]);

                    // $record->assignRole([UserRoleEnum::RESPONSABLE->value]);

                    // $record->update([
                    //     'datas' => [
                    //         'tel' => $record->datas['tel'],
                    //         'visible' => true
                    //     ],
                    // ]);
                }),
        ];
    }
}
