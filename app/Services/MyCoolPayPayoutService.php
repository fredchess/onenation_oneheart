<?php

namespace App\Services;

use App\Exceptions\Payout\PayoutException;
use App\Exceptions\Payout\PayoutUncertainException;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Str;

/**
 * Client HTTP de l'API payout MyCoolPay. Ne touche pas à la base : voir PayoutManager.
 */
class MyCoolPayPayoutService
{
    /**
     * Erreurs cURL garantissant que la requête n'a jamais atteint MyCoolPay
     * (résolution DNS, connexion refusée, échec TLS).
     */
    private const NOT_SENT_CURL_ERRORS = [6, 7, 35];

    private string $baseUrl;
    private Client $client;

    public function __construct(?Client $client = null)
    {
        $publicKey = config('services.mycoolpay.public_key');
        $this->baseUrl = "https://my-coolpay.com/api/{$publicKey}";
        $this->client = $client ?? new Client([
            'connect_timeout' => 10,
            'timeout' => 45,
        ]);
    }

    /**
     * @throws PayoutException
     */
    public function getBalance(): float
    {
        try {
            $response = $this->client->get("{$this->baseUrl}/balance");
        } catch (GuzzleException $e) {
            throw new PayoutException('Impossible de récupérer le solde MyCoolPay : ' . $e->getMessage(), previous: $e);
        }

        $json = json_decode((string) $response->getBody());

        if (! isset($json->balance)) {
            throw new PayoutException('Réponse inattendue de MyCoolPay pour le solde.');
        }

        return (float) $json->balance;
    }

    /**
     * Envoie l'ordre de payout.
     *
     * @return array{http_status:int, transaction_ref:?string, completed:bool, message:?string}
     *
     * @throws PayoutException          refus certain : aucun argent n'est parti
     * @throws PayoutUncertainException aucune réponse : l'argent est peut-être parti
     */
    public function payout(float $amount, string $phone, string $operator, string $appTransactionRef, string $customerName, string $customerEmail): array
    {
        try {
            $response = $this->client->post("{$this->baseUrl}/payout", [
                'json' => [
                    'transaction_amount' => (int) round($amount),
                    'transaction_currency' => 'XAF',
                    'transaction_reason' => 'Versement ONOH',
                    'transaction_operator' => $operator,
                    'app_transaction_ref' => $appTransactionRef,
                    'customer_phone_number' => $phone,
                    'customer_name' => $customerName,
                    'customer_email' => $customerEmail,
                    'customer_lang' => 'fr',
                ],
            ]);
        } catch (ConnectException $e) {
            $errno = $e->getHandlerContext()['errno'] ?? null;

            if (in_array($errno, self::NOT_SENT_CURL_ERRORS, true)) {
                throw new PayoutException('MyCoolPay injoignable : ' . $e->getMessage(), previous: $e);
            }

            throw new PayoutUncertainException('Pas de réponse de MyCoolPay : ' . $e->getMessage(), previous: $e);
        } catch (RequestException $e) {
            if ($e->hasResponse()) {
                throw new PayoutException('MyCoolPay a refusé le versement : ' . $this->errorMessage((string) $e->getResponse()->getBody(), $e->getMessage()), previous: $e);
            }

            throw new PayoutUncertainException('Pas de réponse de MyCoolPay : ' . $e->getMessage(), previous: $e);
        } catch (GuzzleException $e) {
            throw new PayoutUncertainException('Erreur réseau MyCoolPay : ' . $e->getMessage(), previous: $e);
        }

        $statusCode = $response->getStatusCode();
        $body = (string) $response->getBody();
        $json = json_decode($body);

        if (! is_object($json)) {
            throw new PayoutUncertainException("Réponse illisible de MyCoolPay (HTTP {$statusCode}).");
        }

        if (isset($json->status) && strtolower((string) $json->status) !== 'success') {
            throw new PayoutException('MyCoolPay a refusé le versement : ' . $this->errorMessage($body, 'statut ' . $json->status));
        }

        return [
            'http_status' => $statusCode,
            'transaction_ref' => $json->transaction_ref ?? null,
            // 200 : payout exécuté ; 202 : accepté, le résultat arrivera par callback.
            'completed' => $statusCode === 200,
            'message' => $json->message ?? null,
        ];
    }

    public static function generateAppTransactionRef(int $versementId): string
    {
        return sprintf('onoh_payout_%d_%s', $versementId, Str::uuid()->toString());
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

    private function errorMessage(string $body, string $fallback): string
    {
        $json = json_decode($body);

        return is_object($json) && isset($json->message) ? (string) $json->message : $fallback;
    }
}
