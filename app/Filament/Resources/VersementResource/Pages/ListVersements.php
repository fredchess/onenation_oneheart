<?php

namespace App\Filament\Resources\VersementResource\Pages;

use App\Enums\PaymentStatus;
use App\Filament\Resources\VersementResource;
use App\Models\Versement;
use App\Services\MyCoolPayPayoutService;
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
                ->mutateFormDataUsing(function (array $data): array {
                    $data['payment_status'] = PaymentStatus::PENDING->value;
                    $data['initiated_by'] = Auth::id();
                    $data['datas'] = [
                        'phone'    => $data['phone'] ?? null,
                        'operator' => $data['operator'] ?? null,
                        'source'   => $data['source'] ?? 'orphanage',
                    ];
                    unset($data['phone'], $data['operator'], $data['source']);
                    return $data;
                })
                ->after(function (Versement $record): void {
                    $phone    = $record->datas['phone'] ?? null;
                    $operator = $record->datas['operator'] ?? null;

                    try {
                        $service = new MyCoolPayPayoutService();
                        $balance = $service->getBalance();

                        if ($balance < $record->amount) {
                            $record->payment_status = PaymentStatus::FAILED;
                            $record->save();

                            Notification::make()
                                ->danger()
                                ->title('Solde MyCoolPay insuffisant')
                                ->body('Solde disponible : ' . number_format($balance, 0, ',', ' ') . ' XAF')
                                ->persistent()
                                ->send();

                            return;
                        }

                        $service->payout($record, $phone, $operator);

                        Notification::make()
                            ->success()
                            ->title('Versement initié')
                            ->body('Le versement a été soumis à MyCoolPay.')
                            ->send();

                    } catch (\Exception $e) {
                        $record->payment_status = PaymentStatus::FAILED;
                        $record->save();

                        Notification::make()
                            ->danger()
                            ->title('Erreur lors du versement')
                            ->body($e->getMessage())
                            ->persistent()
                            ->send();
                    }
                }),
        ];
    }
}
