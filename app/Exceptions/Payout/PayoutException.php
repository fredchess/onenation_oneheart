<?php

namespace App\Exceptions\Payout;

/**
 * Le versement n'a pas pu être initié (validation, solde, refus de MyCoolPay...). Aucun argent n'est parti.
 */
class PayoutException extends \RuntimeException
{
}
