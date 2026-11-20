<?php

namespace App\Support;

/**
 * Numéros Mobile Money camerounais (MTN MoMo / Orange Money).
 */
class MobileMoneyNumber
{
    public const ORANGE = 'CM_OM';
    public const MTN = 'CM_MOMO';

    public const OPERATORS = [
        self::ORANGE => 'Orange Money (CM_OM)',
        self::MTN => 'MTN Mobile Money (CM_MOMO)',
    ];

    /**
     * Préfixes connus par opérateur (numéros à 9 chiffres commençant par 6).
     * Un préfixe absent de ces listes n'est pas bloqué : on ne vérifie que les incohérences certaines.
     */
    private const PREFIXES = [
        self::MTN => ['67', '650', '651', '652', '653', '654', '680', '681', '682', '683'],
        self::ORANGE => ['69', '655', '656', '657', '658', '659', '640', '686', '687', '688', '689'],
    ];

    /**
     * Ramène un numéro au format local à 9 chiffres (ex. « +237 6 99 00 99 00 » → « 699009900 »).
     * Retourne null si le numéro n'est pas un mobile camerounais valide.
     */
    public static function normalize(?string $phone): ?string
    {
        $digits = preg_replace('/\D+/', '', (string) $phone);

        if (str_starts_with($digits, '00237')) {
            $digits = substr($digits, 5);
        } elseif (strlen($digits) === 12 && str_starts_with($digits, '237')) {
            $digits = substr($digits, 3);
        }

        return preg_match('/^6\d{8}$/', $digits) ? $digits : null;
    }

    public static function detectOperator(string $normalized): ?string
    {
        foreach (self::PREFIXES as $operator => $prefixes) {
            foreach ($prefixes as $prefix) {
                if (str_starts_with($normalized, $prefix)) {
                    return $operator;
                }
            }
        }

        return null;
    }

    /**
     * Message d'erreur en français, ou null si le couple numéro / opérateur est acceptable.
     */
    public static function validationError(?string $phone, ?string $operator): ?string
    {
        $normalized = static::normalize($phone);

        if ($normalized === null) {
            return 'Numéro Mobile Money invalide : 9 chiffres commençant par 6 (ex. 699 00 99 00).';
        }

        if ($operator !== null && ! array_key_exists($operator, self::OPERATORS)) {
            return 'Opérateur Mobile Money inconnu.';
        }

        $detected = static::detectOperator($normalized);

        if ($operator !== null && $detected !== null && $detected !== $operator) {
            return 'Ce numéro est un numéro ' . self::OPERATORS[$detected] . ', pas ' . self::OPERATORS[$operator] . '.';
        }

        return null;
    }

    /**
     * Règle de validation Filament/Laravel ; $operator est résolu au moment de la validation.
     */
    public static function rule(\Closure $operator): \Closure
    {
        return function (string $attribute, $value, \Closure $fail) use ($operator) {
            if (blank($value)) {
                return;
            }

            if ($error = static::validationError($value, $operator())) {
                $fail($error);
            }
        };
    }
}
