<?php

namespace App\Services\Payment;

use App\Models\Donation;
use Illuminate\Http\Request;

class PaymentService
{
    protected $gateway;

    public function __construct(BaseGateway $gateway)
    {
        $this->gateway = $gateway;
    }

    public function processPayment (Request $request, Donation $donation)
    {
        return $this->gateway->pay($request, $donation);
    }

    public function callback (Request $request)
    {
        $this->gateway->callback($request);
    }
}