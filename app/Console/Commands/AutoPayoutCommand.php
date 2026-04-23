<?php

namespace App\Console\Commands;

use App\Enums\PaymentStatus;
use App\Models\AppSetting;
use App\Models\Orphanage;
use App\Models\Versement;
use App\Services\MyCoolPayPayoutService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class AutoPayoutCommand extends Command
{
    protected $signature = 'onoh:auto-payout';
    protected $description = 'Déclenche automatiquement les versements vers les orphelinats';

    public function handle(): int
    {
        $this->info('Démarrage du payout automatique...');

        $service = new MyCoolPayPayoutService();

        try {
            $balance = $service->getBalance();
            $this->info("Solde MyCoolPay disponible : {$balance} XAF");
        } catch (\Exception $e) {
            $this->error('Impossible de récupérer le solde MyCoolPay : ' . $e->getMessage());
            Log::error('Auto-payout: failed to get balance', ['error' => $e->getMessage()]);
            return self::FAILURE;
        }

        $orphanages = Orphanage::all();
        $initiated = 0;
        $skipped = 0;

        foreach ($orphanages as $orphanage) {
            $available = $orphanage->getAvailableDonationAmount();

            if ($available <= 0) {
                $skipped++;
                continue;
            }

            if ($orphanage->hasPendingVersement()) {
                $this->warn("Orphelinat #{$orphanage->id} ({$orphanage->name}) : versement en attente, ignoré.");
                $skipped++;
                continue;
            }

            $phone = $orphanage->data_financial_infos['om_momo'] ?? null;
            $operator = $orphanage->data_financial_infos['payout_operator'] ?? null;

            if (! $phone || ! $operator) {
                $this->warn("Orphelinat #{$orphanage->id} ({$orphanage->name}) : téléphone ou opérateur non configuré, ignoré.");
                $skipped++;
                continue;
            }

            if ($balance < $available) {
                $this->warn("Orphelinat #{$orphanage->id} ({$orphanage->name}) : solde insuffisant ({$balance} XAF disponible, {$available} XAF requis), ignoré.");
                $skipped++;
                continue;
            }

            $versement = Versement::create([
                'orphanage_id' => $orphanage->id,
                'amount' => $available,
                'payment_status' => PaymentStatus::PENDING->value,
                'initiated_by' => null,
                'datas' => ['phone' => $phone, 'operator' => $operator],
            ]);

            try {
                $result = $service->payout($versement, $phone, $operator);
                $balance -= $available;
                $initiated++;
                $this->info("Orphelinat #{$orphanage->id} ({$orphanage->name}) : versement de {$available} XAF initié (status: {$result['status']}).");
            } catch (\Exception $e) {
                $versement->payment_status = PaymentStatus::FAILED;
                $versement->save();
                $this->error("Orphelinat #{$orphanage->id} ({$orphanage->name}) : erreur payout — " . $e->getMessage());
                Log::error('Auto-payout failed', ['orphanage_id' => $orphanage->id, 'error' => $e->getMessage()]);
            }
        }

        $this->info("Terminé. {$initiated} versement(s) initié(s), {$skipped} ignoré(s).");
        Log::info('Auto-payout completed', ['initiated' => $initiated, 'skipped' => $skipped]);

        return self::SUCCESS;
    }
}
