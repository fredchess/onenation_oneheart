<?php

namespace App\Support;

use App\Enums\PaymentStatus;
use App\Models\Orphanage;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

/**
 * Points of the orphanages map (published orphanages with coordinates) and the
 * stats shown when hovering one.
 */
class OrphanageMap
{
    private const CACHE_KEY = 'orphanage-map-points';

    /**
     * @param  array<int, int>|null  $onlyIds  restrict to these orphanages (search results)
     * @return array<int, array<string, mixed>>
     */
    public static function points(?array $onlyIds = null): array
    {
        $points = Cache::remember(self::CACHE_KEY, now()->addMinutes(10), fn () => self::build());

        if ($onlyIds === null) {
            return $points;
        }

        $onlyIds = array_flip($onlyIds);

        return array_values(array_filter($points, fn ($point) => isset($onlyIds[$point['id']])));
    }

    public static function forget(): void
    {
        Cache::forget(self::CACHE_KEY);
    }

    private static function build(): array
    {
        return Orphanage::query()
            ->where('status', 1)
            ->whereNotNull('location')
            ->with(['city', 'media'])
            ->withSum(['donations as raised' => fn ($q) => $q->where('payment_status', PaymentStatus::SUCCESS->value)], 'amount')
            ->orderBy('name')
            ->get()
            ->filter(fn (Orphanage $o) => $o->coordinates() !== null)
            ->map(function (Orphanage $o) {
                $stats = $o->data_stats ?? [];
                $int = fn (string $key) => max(0, (int) ($stats[$key] ?? 0));

                return [
                    'id' => $o->id,
                    'name' => $o->name,
                    'url' => route('public.orphanages.details', ['orphanage_slug' => $o->slug]),
                    'place' => collect([$o->city?->name, Str::limit(trim(strip_tags($o->data_address['localisation'] ?? '')), 40)])->filter()->join(' · '),
                    'city' => $o->city?->name,
                    ...$o->coordinates(),
                    'photo' => $o->getFirstMediaUrl('profile_images') ?: SiteImages::fallbackUrl($o->id),
                    'fallback' => SiteImages::fallbackUrl($o->id),
                    'children' => $int('children_number'),
                    'girls' => $int('girls_number'),
                    'boys' => $int('boys_number'),
                    'ages' => [
                        '0–6 ans' => $int('children_number_0_6'),
                        '7–13 ans' => $int('children_number_7_13'),
                        '14–21 ans' => $int('children_number_14_21'),
                    ],
                    'needs' => array_values($o->filledNeeds()),
                    'raised' => (int) $o->raised,
                ];
            })
            ->values()
            ->all();
    }
}
