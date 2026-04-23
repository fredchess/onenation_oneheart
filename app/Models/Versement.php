<?php

namespace App\Models;

use App\Enums\PaymentStatus;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Versement extends Model
{
    protected $fillable = [
        'orphanage_id',
        'amount',
        'payment_status',
        'datas',
        'initiated_by',
    ];

    protected $casts = [
        'datas' => 'array',
        'payment_status' => PaymentStatus::class,
        'amount' => 'float',
    ];

    public function orphanage(): BelongsTo
    {
        return $this->belongsTo(Orphanage::class);
    }

    public function initiatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'initiated_by');
    }

    /**
     * Fonds disponibles issus des donations sans orphelinat attribué,
     * déduction faite des versements déjà effectués depuis cette source.
     */
    public static function getUnassignedAvailableAmount(): float
    {
        $resetDateRaw = AppSetting::get('payout_counter_reset_at');
        $resetDate = $resetDateRaw ? Carbon::parse($resetDateRaw) : now();

        $totalDonations = (float) Donation::whereNull('orphanage_id')
            ->where('payment_status', PaymentStatus::SUCCESS->value)
            ->where('created_at', '>', $resetDate)
            ->sum('amount');

        $totalPaidOut = (float) static::where('payment_status', PaymentStatus::SUCCESS->value)
            ->where('datas->source', 'unassigned')
            ->sum('amount');

        return max(0.0, $totalDonations - $totalPaidOut);
    }
}
