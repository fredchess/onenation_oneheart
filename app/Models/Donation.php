<?php

namespace App\Models;

use App\Enums\PaymentStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Donation extends Model
{
    use HasFactory;

    protected $casts = [
        'datas' => 'array',
        'payment_status' => PaymentStatus::class,
    ];

    public function orphanage()
    {
        return $this->belongsTo(Orphanage::class);
    }

    public function getPaymentModeLabelAttribute(): ?string
    {
        return match ($this->datas['payment_mode'] ?? null) {
            'momo' => 'OM / MTN MoMo',
            'paypal' => 'PayPal / Carte bancaire',
            default => $this->datas['payment_mode'] ?? null,
        };
    }

    public function getPaymentStatusLabelAttribute(): string
    {
        return $this->payment_status?->label() ?? PaymentStatus::PENDING->label();
    }

    public function getIsSuccessfulAttribute(): bool
    {
        return $this->payment_status === PaymentStatus::SUCCESS;
    }
}
