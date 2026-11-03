<?php

namespace App\Filament\Resources\TeamMemberResource\Pages;

use App\Filament\Resources\TeamMemberResource;
use App\Models\TeamMember;
use Filament\Resources\Pages\CreateRecord;

class CreateTeamMember extends CreateRecord
{
    protected static string $resource = TeamMemberResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Nouveau membre placé en fin de liste
        $data['sort_order'] = (int) TeamMember::max('sort_order') + 1;

        return $data;
    }
}
