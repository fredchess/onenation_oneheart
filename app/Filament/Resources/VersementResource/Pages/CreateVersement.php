<?php

namespace App\Filament\Resources\VersementResource\Pages;

use App\Enums\PaymentStatus;
use App\Filament\Resources\VersementResource;
use App\Services\MyCoolPayPayoutService;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class CreateVersement extends CreateRecord
{
    protected static string $resource = VersementResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['payment_status'] = PaymentStatus::PENDING->value;
        $data['initiated_by'] = Auth::id();

        // Temporarily store phone/operator/source in datas — used in afterCreate for payout
        $data['datas'] = [
            'phone' => $data['phone'] ?? null,
            'operator' => $data['operator'] ?? null,
            'source' => $data['source'] ?? 'orphanage',
        ];

        unset($data['phone'], $data['operator'], $data['source']);

        return $data;
    }

    protected function afterCreate(): void
    {
        $versement = $this->getRecord();
        $phone = $versement->datas['phone'] ?? null;
        $operator = $versement->datas['operator'] ?? null;

        try {
            $service = new MyCoolPayPayoutService();
            $balance = $service->getBalance();

            if ($balance < $versement->amount) {
                $versement->payment_status = PaymentStatus::FAILED;
                $versement->save();

                Notification::make()
                    ->danger()
                    ->title('Solde MyCoolPay insuffisant')
                    ->body('Solde disponible : ' . number_format($balance, 0, ',', ' ') . ' XAF')
                    ->persistent()
                    ->send();

                return;
            }

            $service->payout($versement, $phone, $operator);

            Notification::make()
                ->success()
                ->title('Versement initié')
                ->body('Le versement a été soumis à MyCoolPay.')
                ->send();

        } catch (\Exception $e) {
            $versement->payment_status = PaymentStatus::FAILED;
            $versement->save();

            Notification::make()
                ->danger()
                ->title('Erreur lors du versement')
                ->body($e->getMessage())
                ->persistent()
                ->send();
        }
    }
}
