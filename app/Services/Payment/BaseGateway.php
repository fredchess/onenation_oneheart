<?php

/**
 * Interface for all payment APIs
 */

namespace App\Services\Payment;

use App\Models\Donation;
use Illuminate\Http\Request;

interface BaseGateway
{
    public function pay(Request $request, Donation $donation) : string;
    public function callback(Request $request);
}