<?php

namespace App\Http\Controllers;

use App\Enums\DonationTypeEnum;
use App\Models\Donation;
use App\Services\Payment\PaymentService;
use App\Services\Payment\StripeGateway;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Enum;
use Stripe\StripeClient;

use function MongoDB\BSON\toJSON;

class DonationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        //
        $donations = Donation::all();
        return view("admin.donations.index", compact("donations"));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     * @throws \Exception
     */
    public function store(Request $request)
    {

        $request->validate([
            'name' => 'required',
            'email' => 'required|email',
            'donate_option' => new Enum(DonationTypeEnum::class),
        ]);

        $donation = new Donation;

        $existing = Donation::query()->where([
            'status' => false,
            'datas->email' => $request->email,
            'orphanage_id' => $request->orphanage_id,
        ])->first();

        if ($existing) {
            $donation = $existing;
        }

        if ($request->donate_option == DonationTypeEnum::FINANCIAL->value && $request->payment_mode == 'momo') {
            $request->validate([
                'tel' => 'required',
                'amount' => 'required|numeric'
            ]);
        }
        elseif ($request->donate_option == DonationTypeEnum::FINANCIAL->value) {
            $request->validate([
                'amount_eur' => 'required|numeric'
            ]);
        }
        $donation->amount = ($request->donate_option == DonationTypeEnum::FINANCIAL->value && $request->payment_mode == 'paypal') ? $request->amount_eur * 655 : $request->amount; // Conversion approximative du EUR en XAF lorsque le mode de paiement est PayPal
        $donation->status = 0;
        $datas = [
            "name" => $request->name,
            "email" => $request->email,
            "tel" => $request->tel,
            "payment_mode" => $request->payment_mode,
            "donate_option" => $request->donate_option,
        ];
        $donation->datas = $datas;

        if ($request->orphanage_id)
            $donation->orphanage_id = $request->orphanage_id;

        $donation->save();

        if ($request->donate_option == DonationTypeEnum::FINANCIAL->value && $request->payment_mode == 'momo') {
            $url = sprintf(
                'https://my-coolpay.com/api/%s/paylink',
                env('MY_COOL_PAY_PUBLIC_KEY', '2d851069-b8ce-44c7-8511-4fbf77164cf9')
            );

            try {
                $client = new Client();

                $maxAttempts = 3;

                for ($attempt = 1; $attempt <= $maxAttempts; $attempt++) {
                    $transactionRef = $this->generateAppTransactionRef($donation->id);
                    $body = json_encode([
                        "transaction_amount" => $request->amount,
                        "transaction_currency" => "XAF",
                        "transaction_reason" => "Onoh payment",
                        "app_transaction_ref" => $transactionRef,
                        "customer_phone_number" => $request->tel,
                        "customer_name" => $request->name,
                        "customer_email" => $request->email,
                        "customer_lang" => "fr"
                    ]);

                    try {
                        $req = new \GuzzleHttp\Psr7\Request('POST', $url, ['Content-Type' => 'application/json'], $body);
                        $response = $client->send($req);

                        $json = json_decode($response->getBody()->getContents());

                        if (!isset($json->payment_url)) {
                            return redirect()->back()->with('error', 'Mycoolpay n\'a pas retourné d\'URL de paiement.');
                        }

                        $datas = $donation->datas ?? [];
                        $datas['app_transaction_ref'] = $transactionRef;
                        $datas['mcp_transaction_ref'] = $json->transaction_ref ?? null;
                        $datas['mcp_transaction_status'] = $json->action ?? 'PENDING';
                        $donation->datas = $datas;
                        $donation->save();

                        return redirect($json->payment_url);
                    } catch (RequestException $requestException) {
                        $statusCode = $requestException->getResponse()?->getStatusCode();
                        $responseBody = $requestException->getResponse() ? (string) $requestException->getResponse()->getBody() : '';

                        if (
                            $statusCode === 409
                            && str_contains($responseBody, 'Duplicate transaction reference')
                            && $attempt < $maxAttempts
                        ) {
                            continue;
                        }

                        throw $requestException;
                    }
                }

                return redirect()->back()->with('error', 'Impossible d\'initialiser le paiement Mycoolpay après plusieurs tentatives.');
            } catch (\Exception $e) {
                return redirect()->back()->with('error' ,$e->getMessage());
            }
        } else if ($request->donate_option == DonationTypeEnum::FINANCIAL->value && $request->payment_mode == 'paypal') {
            try {
                $paymentService = new PaymentService(new StripeGateway()); // Faire pareil pour MyCollPay

                $donation->amount = $request->amount_eur; // On reprend le montant en EUR

                $url = $paymentService->processPayment($request, $donation);

                return redirect($url);
            } catch (\Exception $e) {
                Log::error($e->getMessage(), ['key_from_config' => config('payment.stripe.secret_key'), 'key_from_env' => env('STRIPE_SECRET')]);
                return redirect()->back()->with("error", $e->getMessage());
            }
        }

        return redirect()->back()->with("success", "Votre don a bien été enregistré. Nous vous recontacterons afin de finaliser le paiement");
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Donation  $donation
     * @return \Illuminate\Http\Response
     */
    public function show(Donation $donation)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Donation  $donation
     * @return \Illuminate\Http\Response
     */
    public function edit(Donation $donation)
    {
        //
    }

    public function update_status(Request $request)
    {
        $donation = Donation::find($request->donation_id);
        $donation->status = $request->status;
        $donation->save();
        return redirect()->back()->with("success", "Le don a bien été modifié");
    }


    /**
     * Callback for My cool pay payment api
     */
    public function callback_dvXQEdsFNNCcfTYCrvGY(Request $request)
    {
        $key = env("MY_COOL_PAY_PRIVATE_KEY", null);

        if ($key == null) {
            Log::warning('MyCoolPay callback rejected: missing private key.');

            return response('KO', Response::HTTP_BAD_REQUEST);
        }

        $allowedIps = ['15.236.140.89'];
        $requestIps = array_filter(array_merge([$request->ip(), $request->server('REMOTE_ADDR')], $request->ips()));

        if (empty(array_intersect($allowedIps, $requestIps))) {
            Log::warning('MyCoolPay callback rejected: invalid source IP.', ['ips' => $requestIps]);

            return response('KO', Response::HTTP_FORBIDDEN);
        }

        $siganture = md5(
            $request->transaction_ref
                . $request->transaction_type
                . $request->transaction_amount
                . $request->transaction_currency
                . $request->transaction_operator
                . $key
        );

        if ($siganture != $request->signature) {
            Log::warning('MyCoolPay callback rejected: invalid signature.', [
                'app_transaction_ref' => $request->app_transaction_ref,
                'transaction_ref' => $request->transaction_ref,
            ]);

            return response('KO', Response::HTTP_FORBIDDEN);
        }

        $donationId = $this->extractDonationIdFromTransactionRef($request->app_transaction_ref);

        if ($donationId === null) {
            Log::warning('MyCoolPay callback rejected: invalid app transaction ref.', [
                'app_transaction_ref' => $request->app_transaction_ref,
            ]);

            return response('KO', Response::HTTP_BAD_REQUEST);
        }

        $donation = Donation::find($donationId);

        if (! $donation) {
            Log::warning('MyCoolPay callback rejected: donation not found.', [
                'donation_id' => $donationId,
                'app_transaction_ref' => $request->app_transaction_ref,
            ]);

            return response('KO', Response::HTTP_NOT_FOUND);
        }

        $datas = $donation->datas ?? [];
        $datas['app_transaction_ref'] = $request->app_transaction_ref;
        $datas['mcp_transaction_ref'] = $request->transaction_ref;
        $datas['mcp_operator_transaction_ref'] = $request->operator_transaction_ref;
        $datas['mcp_transaction_operator'] = $request->transaction_operator;
        $datas['mcp_transaction_status'] = $request->transaction_status;
        $datas['mcp_transaction_message'] = $request->transaction_message;

        $donation->datas = $datas;
        $donation->status = $request->transaction_status === 'SUCCESS';
        $donation->save();

        return response('OK', Response::HTTP_OK);
    }

    public function callback(Request $request)
    {
        $paymentService = new PaymentService(new StripeGateway());

        $paymentService->callback($request);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Donation  $donation
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Donation $donation)
    {
        $datas = $donation->datas;
        if ($request->amount) {
            $donation->amount = $request->amount;
        }

        if ($request->status) {
            $donation->status = $request->status;
        }

        if ($request->name) {
            $datas["name"] = $request->name;
        }

        if ($request->email) {
            $datas["email"] = $request->email;
        }

        if ($request->tel) {
            $datas["tel"] = $request->tel;
        }

        if ($request->payment_mode) {
            $datas["payment_mode"] = $request->payment_mode;
        }
        $donation->datas = $datas;
        $donation->save();

        return redirect()->back()->with("success", "Ce don a bien été modifié.");
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Donation  $donation
     * @return \Illuminate\Http\Response
     */
    public function destroy(Donation $donation)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Request
     * @return \Illuminate\Http\Response
     */
    public function multipleDestroy(Request $request)
    {
        if ($request->ids) {
            $ids = $request->ids;
            foreach ($ids as $id) {
                $donations = Donation::find($id);
                //ActivityLogger::activity("Suppression de l'équipe ID:".$client->id.' par l\'utilisateur ID:'.Auth::id());
                $donations->delete();
            }
            $message = sizeof($ids) . ' don(s) supprimé(s) avec succès';
        } else {
            $message = "Aucun don n'a été supprimé";
        }
        return redirect()->route("donations.index")->with('success', $message);
    }

    private function generateAppTransactionRef(int $donationId): string
    {
        return sprintf('onoh_%d_%s', $donationId, Str::uuid()->toString());
    }

    private function extractDonationIdFromTransactionRef(?string $appTransactionRef): ?int
    {
        if (! is_string($appTransactionRef)) {
            return null;
        }

        if (! preg_match('/^onoh_(\d+)_/', $appTransactionRef, $matches)) {
            return null;
        }

        return (int) $matches[1];
    }
}
