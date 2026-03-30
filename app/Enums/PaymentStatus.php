<?php

namespace App\Enums;

enum PaymentStatus: string
{
    case PENDING = 'pending';
    case SUCCESS = 'success';
    case FAILED = 'failed';

    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'En attente',
            self::SUCCESS => 'Réussi',
            self::FAILED => 'Échoué',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::PENDING => 'warning',
            self::SUCCESS => 'success',
            self::FAILED => 'danger',
        };
    }
}
