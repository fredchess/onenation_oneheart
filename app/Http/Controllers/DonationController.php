<?php

namespace App\Http\Controllers;

use App\Enums\DonationTypeEnum;
use App\Models\Donation;
use App\Services\Payment\PaymentService;
use App\Services\Payment\StripeGateway;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
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

        $transaction_ref = "onoh_{$donation->id}";

        if ($request->donate_option == DonationTypeEnum::FINANCIAL->value && $request->payment_mode == 'momo') {
            $url = "https://my-coolpay.com/api/2d851069-b8ce-44c7-8511-4fbf77164cf9/paylink";

            $body = json_encode([
                "transaction_amount" => $request->amount,
                "transaction_currency" => "XAF",
                "transaction_reason" => "Onoh payment",
                "app_transaction_ref" => $transaction_ref,
                "customer_phone_number" => $request->tel,
                "customer_name" => $request->name,
                "customer_email" => $request->email,
                "customer_lang" => "fr"
            ]);

            try {
                $client = new Client();
                $req = new \GuzzleHttp\Psr7\Request('POST', $url, ['Content-Type' => 'application/json'], $body);

                $response = $client->send($req);

                $json = json_decode($response->getBody()->getContents());

                if (!isset($json->payment_url)) return redirect()->back();

                return redirect($json->payment_url);
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
        // Check transaction status
        $key = env("MY_COOL_PAY_PRIVATE_KEY", null);

        if ($request->ip() != '15.236.140.89' || $key == null) return;

        $siganture = md5(
            $request->transaction_ref
                . $request->transaction_type
                . $request->transaction_amount
                . $request->transaction_currency
                . $request->transaction_operator
                . $key
        );

        if ($siganture == $request->signature) {
            $donation_id = intval(explode('_', $request->app_transaction_ref)[1]);

            $donation = Donation::find($donation_id);

            if ($donation && $request->transaction_status == 'SUCCESS') {
                $donation->status = 1;

                $donation->save();

                return ['message' => 'ok'];
            }
        }
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
}
