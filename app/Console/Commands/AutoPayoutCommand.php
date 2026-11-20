<?php

namespace App\Console\Commands;

use App\Enums\PaymentStatus;
use App\Exceptions\Payout\PayoutException;
use App\Models\Orphanage;
use App\Models\Versement;
use App\Services\PayoutManager;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class AutoPayoutCommand extends Command
{
    protected $signature = 'onoh:auto-payout';
    protected $description = 'Déclenche automatiquement les versements vers les orphelinats';

    public function handle(PayoutManager $payouts): int
    {
        $this->info('Démarrage du payout automatique...');

        $initiated = 0;
        $skipped = 0;
        $failed = 0;

        foreach (Orphanage::query()->cursor() as $orphanage) {
            $label = "Orphelinat #{$orphanage->id} ({$orphanage->name})";
            $available = $orphanage->getAvailableDonationAmount();

            if ($available < 1) {
                $skipped++;
                continue;
            }

            if ($orphanage->hasPendingVersement()) {
                $this->warn("{$label} : versement en attente, ignoré.");
                $skipped++;
                continue;
            }

            try {
                $versement = $payouts->initiate(
                    $orphanage,
                    $available,
                    $orphanage->data_financial_infos['om_momo'] ?? null,
                    $orphanage->data_financial_infos['payout_operator'] ?? null,
                    Versement::SOURCE_ORPHANAGE,
                );
            } catch (PayoutException $e) {
                $this->warn("{$label} : ignoré — {$e->getMessage()}");
                Log::warning('Auto-payout skipped', ['orphanage_id' => $orphanage->id, 'reason' => $e->getMessage()]);
                $skipped++;
                continue;
            }

            if ($versement->payment_status === PaymentStatus::FAILED) {
                $failed++;
                $this->error("{$label} : échec — " . ($versement->datas['error'] ?? 'erreur inconnue'));
                continue;
            }

            $initiated++;
            $this->info("{$label} : versement de {$versement->amount} XAF initié ({$versement->payment_status->label()}).");
        }

        $this->info("Terminé. {$initiated} versement(s) initié(s), {$failed} échoué(s), {$skipped} ignoré(s).");
        Log::info('Auto-payout completed', ['initiated' => $initiated, 'failed' => $failed, 'skipped' => $skipped]);

        return self::SUCCESS;
    }
}
