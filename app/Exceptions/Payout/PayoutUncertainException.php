<?php

namespace App\Exceptions\Payout;

/**
 * La requête a peut-être atteint MyCoolPay mais aucune réponse n'a été reçue (timeout, connexion coupée).
 * Le versement doit rester « en attente » jusqu'au callback ou à une vérification manuelle.
 */
class PayoutUncertainException extends \RuntimeException
{
}
