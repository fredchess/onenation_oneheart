<?php

namespace App\Services;

use App\Enums\PaymentStatus;
use App\Models\Versement;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class MyCoolPayPayoutService
{
    private string $baseUrl;
    private Client $client;

    public function __construct()
    {
        $publicKey = config('services.mycoolpay.public_key');
        $this->baseUrl = "https://my-coolpay.com/api/{$publicKey}";
        $this->client = new Client();
    }

    public function getBalance(): float
    {
        $response = $this->client->get("{$this->baseUrl}/balance");
        $json = json_decode($response->getBody()->getContents());

        return (float) ($json->balance ?? 0);
    }

    /**
     * Initiate a payout for a versement.
     * Returns ['status' => 200|202, 'transaction_ref' => '...'] on success.
     * Throws on error.
     */
    public function payout(Versement $versement, string $phone, string $operator): array
    {
        $orphanage = $versement->orphanage;
        $appTransactionRef = $this->generateAppTransactionRef($versement->id);

        $body = json_encode([
            'transaction_amount' => $versement->amount,
            'transaction_currency' => 'XAF',
            'transaction_reason' => 'Versement ONOH',
            'transaction_operator' => $operator,
            'app_transaction_ref' => $appTransactionRef,
            'customer_phone_number' => $phone,
            'customer_name' => $orphanage->name ?? 'Orphelinat',
            'customer_email' => $orphanage->data_identity['email'] ?? 'contact@onoh.org',
            'customer_lang' => 'fr',
        ]);

        $req = new \GuzzleHttp\Psr7\Request('POST', "{$this->baseUrl}/payout", ['Content-Type' => 'application/json'], $body);
        $response = $this->client->send($req);
        $statusCode = $response->getStatusCode();
        $json = json_decode($response->getBody()->getContents());

        $datas = $versement->datas ?? [];
        $datas['app_transaction_ref'] = $appTransactionRef;
        $datas['mcp_transaction_ref'] = $json->transaction_ref ?? null;
        $datas['phone'] = $phone;
        $datas['operator'] = $operator;
        $versement->datas = $datas;

        if ($statusCode === 200) {
            $versement->payment_status = PaymentStatus::SUCCESS;
        }
        // 202 stays PENDING — callback will update

        $versement->save();

        Log::info('MyCoolPay payout initiated', [
            'versement_id' => $versement->id,
            'orphanage_id' => $versement->orphanage_id,
            'amount' => $versement->amount,
            'status_code' => $statusCode,
            'transaction_ref' => $json->transaction_ref ?? null,
        ]);

        return ['status' => $statusCode, 'transaction_ref' => $json->transaction_ref ?? null];
    }

    public static function extractVersementIdFromTransactionRef(?string $appTransactionRef): ?int
    {
        if (! is_string($appTransactionRef)) {
            return null;
        }

        if (! preg_match('/^onoh_payout_(\d+)_/', $appTransactionRef, $matches)) {
            return null;
        }

        return (int) $matches[1];
    }

    private function generateAppTransactionRef(int $versementId): string
    {
        return sprintf('onoh_payout_%d_%s', $versementId, Str::uuid()->toString());
    }
}
