<?php

namespace App\Filament\Resources\OrphanageResource\Pages;

use App\Enums\UserRoleEnum;
use App\Filament\Resources\OrphanageResource;
use App\Models\User;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Auth;

class EditOrphanage extends EditRecord
{
    protected static string $resource = OrphanageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        /** @var User $user */
        $user = Auth::user();

        // Un responsable ne peut pas modifier les coordonnées de versement : on conserve celles en base.
        if (! $user->hasRole([UserRoleEnum::ADMIN->value, UserRoleEnum::SUPER_ADMIN->value])) {
            $data['data_financial_infos'] = $this->getRecord()->data_financial_infos;
        }

        return $data;
    }
}
