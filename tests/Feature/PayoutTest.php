<?php

namespace Tests\Feature;

use App\Enums\PaymentStatus;
use App\Exceptions\Payout\PayoutException;
use App\Exceptions\Payout\PayoutUncertainException;
use App\Models\AppSetting;
use App\Models\Donation;
use App\Models\Orphanage;
use App\Models\Versement;
use App\Services\MyCoolPayPayoutService;
use App\Services\PayoutManager;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\TestCase;

class PayoutTest extends TestCase
{
    use RefreshDatabase;

    private Orphanage $orphanage;

    protected function setUp(): void
    {
        parent::setUp();

        AppSetting::set('payout_counter_reset_at', now()->subDay()->toISOString());

        $this->orphanage = Orphanage::create([
            'name' => 'Orphelinat Test',
            'data_identity' => ['name' => 'Orphelinat Test'],
            'data_financial_infos' => ['om_momo' => '+237 699 00 99 00', 'payout_operator' => 'CM_OM'],
        ]);
    }

    private function donate(?Orphanage $orphanage, float $amount, PaymentStatus $status = PaymentStatus::SUCCESS): void
    {
        $donation = new Donation();
        $donation->amount = $amount;
        $donation->payment_status = $status;
        $donation->orphanage_id = $orphanage?->id;
        $donation->datas = [];
        $donation->save();
    }

    private function versement(float $amount, PaymentStatus $status, ?string $source = null): Versement
    {
        return Versement::create([
            'orphanage_id' => $this->orphanage->id,
            'amount' => $amount,
            'payment_status' => $status->value,
            'datas' => $source ? ['source' => $source] : ['phone' => '699009900'],
        ]);
    }

    private function fakeGateway(float $balance = 1_000_000): Mockery\MockInterface
    {
        $gateway = Mockery::mock(MyCoolPayPayoutService::class);
        $gateway->shouldReceive('getBalance')->andReturn($balance);
        $this->app->instance(MyCoolPayPayoutService::class, $gateway);

        return $gateway;
    }

    public function test_partial_payout_keeps_the_remainder_available(): void
    {
        $this->donate($this->orphanage, 50_000);
        $this->versement(30_000, PaymentStatus::SUCCESS, Versement::SOURCE_ORPHANAGE);

        $this->assertSame(20_000.0, $this->orphanage->getAvailableDonationAmount());
    }

    public function test_unassigned_payout_does_not_consume_orphanage_funds(): void
    {
        $this->donate($this->orphanage, 50_000);
        $this->donate(null, 10_000);
        $this->versement(10_000, PaymentStatus::SUCCESS, Versement::SOURCE_UNASSIGNED);

        $this->assertSame(50_000.0, $this->orphanage->getAvailableDonationAmount());
        $this->assertSame(0.0, Versement::getUnassignedAvailableAmount());
    }

    public function test_pending_payouts_are_deducted_and_failed_ones_are_not(): void
    {
        $this->donate($this->orphanage, 50_000);
        $this->donate($this->orphanage, 99_000, PaymentStatus::PENDING);
        $this->versement(20_000, PaymentStatus::PENDING);
        $this->versement(30_000, PaymentStatus::FAILED);

        $this->assertSame(30_000.0, $this->orphanage->getAvailableDonationAmount());
    }

    public function test_initiate_sends_normalized_number_and_marks_success(): void
    {
        $this->donate($this->orphanage, 50_000);
        $gateway = $this->fakeGateway();
        $gateway->shouldReceive('payout')
            ->once()
            ->withArgs(fn ($amount, $phone, $operator) => $amount === 40_000.0 && $phone === '699009900' && $operator === 'CM_OM')
            ->andReturn(['http_status' => 200, 'transaction_ref' => 'mcp-1', 'completed' => true, 'message' => null]);

        $versement = app(PayoutManager::class)->initiate($this->orphanage, 40_000, '+237 699 00 99 00', 'CM_OM');

        $this->assertSame(PaymentStatus::SUCCESS, $versement->payment_status);
        $this->assertSame('mcp-1', $versement->datas['mcp_transaction_ref']);
        $this->assertSame(10_000.0, $this->orphanage->getAvailableDonationAmount());
    }

    public function test_initiate_refuses_amount_above_available_funds(): void
    {
        $this->donate($this->orphanage, 5_000);
        $this->versement(4_000, PaymentStatus::PENDING);
        $gateway = $this->fakeGateway();
        $gateway->shouldNotReceive('payout');

        $this->expectException(PayoutException::class);

        try {
            app(PayoutManager::class)->initiate($this->orphanage, 2_000, '699009900', 'CM_OM');
        } finally {
            $this->assertSame(1, Versement::count());
        }
    }

    public function test_initiate_refuses_number_of_the_wrong_operator(): void
    {
        $this->donate($this->orphanage, 5_000);
        $this->fakeGateway()->shouldNotReceive('payout');

        $this->expectException(PayoutException::class);

        app(PayoutManager::class)->initiate($this->orphanage, 5_000, '677000000', 'CM_OM');
    }

    public function test_rejected_payout_is_failed_and_frees_the_funds(): void
    {
        $this->donate($this->orphanage, 5_000);
        $this->fakeGateway()->shouldReceive('payout')->andThrow(new PayoutException('Numéro inconnu'));

        $versement = app(PayoutManager::class)->initiate($this->orphanage, 5_000, '699009900', 'CM_OM');

        $this->assertSame(PaymentStatus::FAILED, $versement->payment_status);
        $this->assertSame(5_000.0, $this->orphanage->getAvailableDonationAmount());
    }

    public function test_uncertain_payout_stays_pending_and_keeps_funds_blocked(): void
    {
        $this->donate($this->orphanage, 5_000);
        $this->fakeGateway()->shouldReceive('payout')->andThrow(new PayoutUncertainException('timeout'));

        $versement = app(PayoutManager::class)->initiate($this->orphanage, 5_000, '699009900', 'CM_OM');

        $this->assertSame(PaymentStatus::PENDING, $versement->payment_status);
        $this->assertTrue($versement->datas['needs_check']);
        $this->assertSame(0.0, $this->orphanage->getAvailableDonationAmount());
    }

    public function test_auto_payout_command_pays_available_funds(): void
    {
        $this->donate($this->orphanage, 15_000);
        $this->fakeGateway()->shouldReceive('payout')
            ->once()
            ->andReturn(['http_status' => 202, 'transaction_ref' => 'mcp-2', 'completed' => false, 'message' => null]);

        $this->artisan('onoh:auto-payout')->assertSuccessful();

        $versement = Versement::sole();
        $this->assertSame(15_000.0, $versement->amount);
        $this->assertSame(PaymentStatus::PENDING, $versement->payment_status);
        $this->assertSame(Versement::SOURCE_ORPHANAGE, $versement->datas['source']);
    }

    private function postPayoutCallback(Versement $versement, string $status): \Illuminate\Testing\TestResponse
    {
        config()->set('services.mycoolpay.private_key', 'test-private-key');
        config()->set('services.mycoolpay.allowed_ips', '');

        $payload = [
            'app_transaction_ref' => "onoh_payout_{$versement->id}_abc",
            'transaction_ref' => 'mcp-ref',
            'transaction_type' => 'PAYOUT',
            'transaction_amount' => (string) (int) $versement->amount,
            'transaction_currency' => 'XAF',
            'transaction_operator' => 'CM_OM',
            'transaction_status' => $status,
        ];
        $payload['signature'] = md5($payload['transaction_ref'] . $payload['transaction_type'] . $payload['transaction_amount']
            . $payload['transaction_currency'] . $payload['transaction_operator'] . 'test-private-key');

        return $this->post(route('callback'), $payload);
    }

    public function test_callback_finalizes_pending_payout(): void
    {
        $versement = $this->versement(5_000, PaymentStatus::PENDING);

        $this->postPayoutCallback($versement, 'SUCCESS')->assertOk();

        $this->assertSame(PaymentStatus::SUCCESS, $versement->fresh()->payment_status);
    }

    public function test_intermediate_callback_does_not_overwrite_final_status(): void
    {
        $versement = $this->versement(5_000, PaymentStatus::SUCCESS);

        $this->postPayoutCallback($versement, 'PENDING')->assertOk();

        $this->assertSame(PaymentStatus::SUCCESS, $versement->fresh()->payment_status);
    }
}
