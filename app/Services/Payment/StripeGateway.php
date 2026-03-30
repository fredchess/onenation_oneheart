<?php


namespace App\Services\Payment;

use App\Enums\PaymentStatus;
use App\Models\Donation;
use Illuminate\Http\Request;
use Stripe\StripeClient;

class StripeGateway implements BaseGateway
{
    public function pay(Request $request, Donation $donation) : string
    {
        $key = config('payment.stripe.secret_key');

        $stripe = new StripeClient($key);

        $session = $stripe->checkout->sessions->create([
            'success_url' => $donation->orphanage_id ? route('public.orphanages.details', $donation->orphanage->slug)."?success=true" : route('public.home')."?success=true",
            'cancel_url' => $donation->orphanage_id ? route('public.orphanages.details', $donation->orphanage->slug)."?success=false" : route('public.home')."?success=false",
            'currency' => 'EUR',
            'metadata' => [
                'donation_id' => $donation->id
            ],
            'mode' => 'payment',
            'line_items' => [[
                'price_data' => [
                    'currency' => 'EUR',
                    'product_data' => [
                        'name' => 'Donation'
                    ],
                    'unit_amount_decimal' => $donation->amount * 100
                ],
                'quantity' => 1
            ]],
            "customer_email" => $request->email
        ]);

        return $session->url;
    }

    public function callback(Request $request) 
    {
        $stripe = new StripeClient(env("STRIPE_SECRET"));

        $session = $stripe->checkout->sessions->retrieve($request->data['object']['id']);

        if ( $session != null && $session->status == 'complete') {
            $donation = Donation::find($session->metadata->donation_id);

            if ($donation) {
                $donation->payment_status = PaymentStatus::SUCCESS;
                $donation->save();
            }
        } elseif ($session != null && isset($session->metadata->donation_id)) {
            $donation = Donation::find($session->metadata->donation_id);

            if ($donation) {
                $donation->payment_status = PaymentStatus::FAILED;
                $donation->save();
            }
        } elseif ($session == null) {
            return;
        }
    }
}
