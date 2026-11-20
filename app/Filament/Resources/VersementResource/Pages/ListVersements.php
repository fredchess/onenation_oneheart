<?php

namespace App\Filament\Resources\VersementResource\Pages;

use App\Exceptions\Payout\PayoutException;
use App\Filament\Resources\VersementResource;
use App\Models\Orphanage;
use App\Services\PayoutManager;
use Filament\Actions\CreateAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Auth;

class ListVersements extends ListRecords
{
    protected static string $resource = VersementResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Nouveau versement')
                ->successNotification(null)
                ->using(function (array $data, CreateAction $action) {
                    try {
                        $versement = app(PayoutManager::class)->initiate(
                            Orphanage::findOrFail($data['orphanage_id']),
                            (float) $data['amount'],
                            $data['phone'] ?? null,
                            $data['operator'] ?? null,
                            $data['source'] ?? 'orphanage',
                            Auth::id(),
                        );
                    } catch (PayoutException $e) {
                        Notification::make()
                            ->danger()
                            ->title('Versement impossible')
                            ->body($e->getMessage())
                            ->persistent()
                            ->send();

                        $action->halt();
                    }

                    VersementResource::notifyPayoutResult($versement);

                    return $versement;
                }),
        ];
    }
}
