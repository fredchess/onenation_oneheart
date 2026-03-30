<?php

namespace App\Filament\Resources\DonationResource\Pages;

use App\Enums\PaymentStatus;
use App\Filament\Resources\DonationResource;
use App\Models\Donation;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageDonations extends ManageRecords
{
    protected static string $resource = DonationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('deleteFailedPayments')
                ->label('Supprimer les paiements échoués')
                ->color('danger')
                ->requiresConfirmation()
                ->action(function (): void {
                    Donation::query()
                        ->where('payment_status', PaymentStatus::FAILED)
                        ->delete();
                }),
        ];
    }
}
