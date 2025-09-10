<?php

namespace App\Enums;

enum DonationTypeEnum : string
{
    case FINANCIAL = 'financial';
    case CLOTHES = 'clothes';
    case FOOD = 'food';
    case SPONSORING = 'sponsoring';

    public function label() : string
    {
        return match ($this) {
            self::FINANCIAL => 'Don financier',
            self::FOOD => 'Don alimentaire',
            self::CLOTHES => 'Don vestimentaire',
            self::SPONSORING => "Parrainage d'enfant",
        };
    }
}
