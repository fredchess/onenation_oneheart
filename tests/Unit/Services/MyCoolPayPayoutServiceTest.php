<?php

namespace Tests\Unit\Services;

use App\Exceptions\Payout\PayoutException;
use App\Exceptions\Payout\PayoutUncertainException;
use App\Services\MyCoolPayPayoutService;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Tests\TestCase;

class MyCoolPayPayoutServiceTest extends TestCase
{
    private function service(array $queue): MyCoolPayPayoutService
    {
        return new MyCoolPayPayoutService(new Client(['handler' => HandlerStack::create(new MockHandler($queue))]));
    }

    private function pay(MyCoolPayPayoutService $service): array
    {
        return $service->payout(5000, '699009900', 'CM_OM', 'onoh_payout_1_x', 'Test', 'test@example.com');
    }

    public function test_200_success_is_completed(): void
    {
        $result = $this->pay($this->service([new Response(200, [], '{"status":"success","transaction_ref":"abc"}')]));

        $this->assertTrue($result['completed']);
        $this->assertSame('abc', $result['transaction_ref']);
    }

    public function test_202_is_accepted_but_not_completed(): void
    {
        $result = $this->pay($this->service([new Response(202, [], '{"status":"success","transaction_ref":"abc"}')]));

        $this->assertFalse($result['completed']);
    }

    public function test_error_status_in_body_is_a_rejection(): void
    {
        $this->expectException(PayoutException::class);
        $this->expectExceptionMessage('Solde insuffisant');

        $this->pay($this->service([new Response(200, [], '{"status":"error","message":"Solde insuffisant"}')]));
    }

    public function test_http_error_is_a_rejection(): void
    {
        $this->expectException(PayoutException::class);

        $this->pay($this->service([new Response(400, [], '{"status":"error","message":"Bad phone"}')]));
    }

    public function test_connection_refused_is_a_rejection(): void
    {
        $this->expectException(PayoutException::class);

        $this->pay($this->service([new ConnectException('refused', new Request('POST', 'x'), null, ['errno' => 7])]));
    }

    public function test_timeout_is_uncertain(): void
    {
        $this->expectException(PayoutUncertainException::class);

        $this->pay($this->service([new ConnectException('timeout', new Request('POST', 'x'), null, ['errno' => 28])]));
    }
}
