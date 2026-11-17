<?php

namespace App\Support;

/**
 * Reads coordinates out of a Google Maps embed (<iframe src="https://www.google.com/maps/embed?pb=…">).
 *
 * The "pb" parameter holds the map centre as !2d<lng>!3d<lat>. When the embed
 * targets a dropped pin, it also holds the exact point as a base64 DMS label
 * (!2z…, e.g. 3°52'21.1"N 11°26'03.1"E), which is preferred.
 */
class GoogleMapsEmbed
{
    /** Rough bounding box of Cameroon, to reject obviously wrong points. */
    private const BOUNDS = ['lat' => [1.5, 13.2], 'lng' => [8.3, 16.3]];

    /**
     * @return array{lat: float, lng: float}|null
     */
    public static function coordinates(?string $embed): ?array
    {
        if (! $embed) {
            return null;
        }

        $embed = html_entity_decode(urldecode($embed));

        $point = self::fromDms($embed)
            ?? self::fromEmbeddedDmsLabel($embed)
            ?? self::match('/!3d(-?\d+(?:\.\d+)?)!4d(-?\d+(?:\.\d+)?)/', $embed, 1, 2) // pin of a /maps/place/ link
            ?? self::match('/@(-?\d+(?:\.\d+)?),(-?\d+(?:\.\d+)?)/', $embed, 1, 2)     // view of a /maps/ link
            ?? self::match('/!2d(-?\d+(?:\.\d+)?)!3d(-?\d+(?:\.\d+)?)/', $embed, 2, 1); // centre of an embed

        return $point && self::inCameroon($point) ? $point : null;
    }

    private static function match(string $pattern, string $subject, int $latGroup, int $lngGroup): ?array
    {
        if (preg_match($pattern, $subject, $m)) {
            return ['lat' => round((float) $m[$latGroup], 6), 'lng' => round((float) $m[$lngGroup], 6)];
        }

        return null;
    }

    /** DMS label hidden in an embed as base64 (!2z…). */
    private static function fromEmbeddedDmsLabel(string $embed): ?array
    {
        if (! preg_match('/!2z([A-Za-z0-9+\/_-]+=*)/', $embed, $m)) {
            return null;
        }

        $label = base64_decode(strtr($m[1], '-_', '+/'), true);

        return $label === false ? null : self::fromDms($label);
    }

    /** Plain DMS text, e.g. 4°02'43.8"N+11°34'05.9"E in a /maps/place/ link. */
    private static function fromDms(string $text): ?array
    {
        if (! preg_match('/(\d+)°(\d+)\'([\d.]+)"([NS])[\s+,]*(\d+)°(\d+)\'([\d.]+)"([EW])/u', $text, $d)) {
            return null;
        }

        $lat = ($d[1] + $d[2] / 60 + $d[3] / 3600) * ($d[4] === 'S' ? -1 : 1);
        $lng = ($d[5] + $d[6] / 60 + $d[7] / 3600) * ($d[8] === 'W' ? -1 : 1);

        return ['lat' => round($lat, 6), 'lng' => round($lng, 6)];
    }

    private static function inCameroon(array $point): bool
    {
        return $point['lat'] >= self::BOUNDS['lat'][0] && $point['lat'] <= self::BOUNDS['lat'][1]
            && $point['lng'] >= self::BOUNDS['lng'][0] && $point['lng'] <= self::BOUNDS['lng'][1];
    }
}
