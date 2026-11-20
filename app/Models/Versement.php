<?php

namespace App\Models;

use App\Enums\PaymentStatus;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Versement extends Model
{
    public const SOURCE_ORPHANAGE = 'orphanage';
    public const SOURCE_UNASSIGNED = 'unassigned';

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

    public function source(): string
    {
        return $this->datas['source'] ?? self::SOURCE_ORPHANAGE;
    }

    /**
     * Date à partir de laquelle dons et versements sont comptabilisés.
     */
    public static function payoutResetDate(): Carbon
    {
        $raw = AppSetting::get('payout_counter_reset_at');

        return $raw ? Carbon::parse($raw) : now();
    }

    /**
     * Versements qui engagent des fonds : réussis, ou en attente (l'argent est peut-être déjà parti).
     */
    public function scopeCommitted(Builder $query): Builder
    {
        return $query->whereIn('payment_status', [PaymentStatus::SUCCESS->value, PaymentStatus::PENDING->value]);
    }

    public function scopeSinceReset(Builder $query): Builder
    {
        return $query->where('created_at', '>', static::payoutResetDate());
    }

    /**
     * Les anciens versements (et ceux du payout automatique historique) n'ont pas de source : ils
     * proviennent des fonds de l'orphelinat.
     */
    public function scopeFromOrphanageFunds(Builder $query): Builder
    {
        return $query->where(fn (Builder $q) => $q
            ->whereNull('datas->source')
            ->orWhere('datas->source', self::SOURCE_ORPHANAGE));
    }

    public function scopeFromUnassignedFunds(Builder $query): Builder
    {
        return $query->where('datas->source', self::SOURCE_UNASSIGNED);
    }

    /**
     * Fonds disponibles issus des donations sans orphelinat attribué,
     * déduction faite des versements réussis ou en cours depuis cette source.
     */
    public static function getUnassignedAvailableAmount(): float
    {
        $since = static::payoutResetDate();

        $totalDonations = (float) Donation::whereNull('orphanage_id')
            ->where('payment_status', PaymentStatus::SUCCESS->value)
            ->where('created_at', '>', $since)
            ->sum('amount');

        $totalCommitted = (float) static::query()
            ->committed()
            ->fromUnassignedFunds()
            ->where('created_at', '>', $since)
            ->sum('amount');

        return max(0.0, $totalDonations - $totalCommitted);
    }
}
