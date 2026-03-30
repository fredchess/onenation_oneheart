<?php

namespace Tests\Feature;

use App\Enums\PaymentStatus;
use App\Models\Donation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DonationCallbackTest extends TestCase
{
    use RefreshDatabase;

    public function test_mycoolpay_callback_marks_donation_as_received(): void
    {
        putenv('MY_COOL_PAY_PRIVATE_KEY=test-private-key');
        $_ENV['MY_COOL_PAY_PRIVATE_KEY'] = 'test-private-key';
        config()->set('services.mycoolpay.private_key', 'test-private-key');

        $donation = new Donation();
        $donation->amount = 5000;
        $donation->payment_status = PaymentStatus::PENDING;
        $donation->datas = [
            'email' => 'donor@example.com',
            'payment_mode' => 'momo',
        ];
        $donation->save();

        $payload = [
            'app_transaction_ref' => "onoh_{$donation->id}_abc",
            'operator_transaction_ref' => 'operator-ref',
            'transaction_ref' => 'mcp-ref',
            'transaction_type' => 'PAYIN',
            'transaction_amount' => '5000',
            'transaction_currency' => 'XAF',
            'transaction_operator' => 'CM_MOMO',
            'transaction_status' => 'SUCCESS',
            'transaction_message' => 'Paid',
        ];

        $payload['signature'] = md5(
            $payload['transaction_ref']
            . $payload['transaction_type']
            . $payload['transaction_amount']
            . $payload['transaction_currency']
            . $payload['transaction_operator']
            . 'test-private-key'
        );

        $response = $this
            ->withServerVariables(['REMOTE_ADDR' => '15.236.140.89'])
            ->post(route('callback'), $payload);

        $response->assertOk();
        $response->assertSeeText('OK');

        $donation->refresh();

        $this->assertSame(PaymentStatus::SUCCESS, $donation->payment_status);
        $this->assertSame('SUCCESS', $donation->datas['mcp_transaction_status']);
        $this->assertSame('mcp-ref', $donation->datas['mcp_transaction_ref']);
    }

    public function test_mycoolpay_callback_rejects_invalid_signature(): void
    {
        putenv('MY_COOL_PAY_PRIVATE_KEY=test-private-key');
        $_ENV['MY_COOL_PAY_PRIVATE_KEY'] = 'test-private-key';
        config()->set('services.mycoolpay.private_key', 'test-private-key');

        $donation = new Donation();
        $donation->amount = 5000;
        $donation->payment_status = PaymentStatus::PENDING;
        $donation->datas = [
            'email' => 'donor@example.com',
            'payment_mode' => 'momo',
        ];
        $donation->save();

        $response = $this
            ->withServerVariables(['REMOTE_ADDR' => '15.236.140.89'])
            ->post(route('callback'), [
                'app_transaction_ref' => "onoh_{$donation->id}_abc",
                'transaction_ref' => 'mcp-ref',
                'transaction_type' => 'PAYIN',
                'transaction_amount' => '5000',
                'transaction_currency' => 'XAF',
                'transaction_operator' => 'CM_MOMO',
                'transaction_status' => 'SUCCESS',
                'signature' => 'invalid-signature',
            ]);

        $response->assertForbidden();
        $response->assertSeeText('KO');

        $donation->refresh();

        $this->assertSame(PaymentStatus::PENDING, $donation->payment_status);
    }

    public function test_mycoolpay_callback_marks_failed_payment_as_failed(): void
    {
        putenv('MY_COOL_PAY_PRIVATE_KEY=test-private-key');
        $_ENV['MY_COOL_PAY_PRIVATE_KEY'] = 'test-private-key';
        config()->set('services.mycoolpay.private_key', 'test-private-key');

        $donation = new Donation();
        $donation->amount = 5000;
        $donation->payment_status = PaymentStatus::PENDING;
        $donation->datas = [
            'email' => 'donor@example.com',
            'payment_mode' => 'momo',
        ];
        $donation->save();

        $payload = [
            'app_transaction_ref' => "onoh_{$donation->id}_abc",
            'operator_transaction_ref' => 'operator-ref',
            'transaction_ref' => 'mcp-ref',
            'transaction_type' => 'PAYIN',
            'transaction_amount' => '5000',
            'transaction_currency' => 'XAF',
            'transaction_operator' => 'CM_MOMO',
            'transaction_status' => 'FAILED',
            'transaction_message' => 'Payment failed',
        ];

        $payload['signature'] = md5(
            $payload['transaction_ref']
            . $payload['transaction_type']
            . $payload['transaction_amount']
            . $payload['transaction_currency']
            . $payload['transaction_operator']
            . 'test-private-key'
        );

        $response = $this
            ->withServerVariables(['REMOTE_ADDR' => '15.236.140.89'])
            ->post(route('callback'), $payload);

        $response->assertOk();

        $donation->refresh();

        $this->assertSame(PaymentStatus::FAILED, $donation->payment_status);
    }
}
