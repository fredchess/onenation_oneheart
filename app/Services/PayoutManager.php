<?php

namespace App\Services;

use App\Enums\PaymentStatus;
use App\Exceptions\Payout\PayoutException;
use App\Exceptions\Payout\PayoutUncertainException;
use App\Models\AppSetting;
use App\Models\Orphanage;
use App\Models\Versement;
use App\Support\MobileMoneyNumber;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Point d'entrée unique pour envoyer de l'argent à un orphelinat (manuel ou automatique).
 */
class PayoutManager
{
    public function __construct(private MyCoolPayPayoutService $gateway)
    {
    }

    /**
     * Crée le versement et l'envoie à MyCoolPay.
     *
     * Retourne le versement dans son état final connu :
     *  - SUCCESS : payout exécuté ;
     *  - PENDING : accepté par MyCoolPay (callback attendu) ou résultat incertain (datas.needs_check) ;
     *  - FAILED  : refusé par MyCoolPay (datas.error).
     *
     * @throws PayoutException si le versement est refusé avant tout envoi (aucun enregistrement créé).
     */
    public function initiate(
        Orphanage $orphanage,
        float $amount,
        ?string $phone,
        ?string $operator,
        string $source = Versement::SOURCE_ORPHANAGE,
        ?int $initiatedBy = null,
    ): Versement {
        $amount = round($amount);

        if ($amount < 1) {
            throw new PayoutException('Le montant doit être supérieur à 0.');
        }

        if (! in_array($source, [Versement::SOURCE_ORPHANAGE, Versement::SOURCE_UNASSIGNED], true)) {
            throw new PayoutException('Source des fonds inconnue.');
        }

        if ($error = MobileMoneyNumber::validationError($phone, $operator)) {
            throw new PayoutException($error);
        }

        if (blank($operator)) {
            throw new PayoutException('Opérateur Mobile Money non renseigné.');
        }

        $phone = MobileMoneyNumber::normalize($phone);

        $balance = $this->gateway->getBalance();
        if ($balance < $amount) {
            throw new PayoutException('Solde MyCoolPay insuffisant : ' . number_format($balance, 0, ',', ' ') . ' XAF disponibles.');
        }

        $versement = DB::transaction(function () use ($orphanage, $amount, $phone, $operator, $source, $initiatedBy) {
            // Verrou global : deux versements simultanés ne peuvent pas consommer les mêmes fonds.
            AppSetting::query()->whereKey('payout_counter_reset_at')->lockForUpdate()->first();

            $available = $source === Versement::SOURCE_UNASSIGNED
                ? Versement::getUnassignedAvailableAmount()
                : $orphanage->getAvailableDonationAmount();

            if ($amount > $available) {
                throw new PayoutException('Le montant dépasse les fonds disponibles (' . number_format($available, 0, ',', ' ') . ' XAF).');
            }

            return Versement::create([
                'orphanage_id' => $orphanage->id,
                'amount' => $amount,
                'payment_status' => PaymentStatus::PENDING->value,
                'initiated_by' => $initiatedBy,
                'datas' => [
                    'phone' => $phone,
                    'operator' => $operator,
                    'source' => $source,
                ],
            ]);
        });

        return $this->send($versement, $orphanage);
    }

    private function send(Versement $versement, Orphanage $orphanage): Versement
    {
        $datas = $versement->datas;
        $datas['app_transaction_ref'] = MyCoolPayPayoutService::generateAppTransactionRef($versement->id);
        $versement->datas = $datas;
        $versement->save();

        try {
            $result = $this->gateway->payout(
                $versement->amount,
                $datas['phone'],
                $datas['operator'],
                $datas['app_transaction_ref'],
                $orphanage->name ?: 'Orphelinat',
                $orphanage->data_identity['email'] ?? 'contact@onoh.org',
            );

            $datas['mcp_transaction_ref'] = $result['transaction_ref'];
            $versement->payment_status = $result['completed'] ? PaymentStatus::SUCCESS : PaymentStatus::PENDING;
        } catch (PayoutUncertainException $e) {
            // L'argent est peut-être parti : on garde les fonds bloqués jusqu'au callback ou à une vérification.
            $datas['needs_check'] = true;
            $datas['error'] = $e->getMessage();
        } catch (PayoutException $e) {
            $datas['error'] = $e->getMessage();
            $versement->payment_status = PaymentStatus::FAILED;
        }

        $versement->datas = $datas;
        $versement->save();

        Log::info('MyCoolPay payout initiated', [
            'versement_id' => $versement->id,
            'orphanage_id' => $versement->orphanage_id,
            'amount' => $versement->amount,
            'source' => $datas['source'],
            'status' => $versement->payment_status->value,
            'error' => $datas['error'] ?? null,
        ]);

        return $versement;
    }
}
