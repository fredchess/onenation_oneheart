<?php

namespace App\Support;

use App\Models\AppSetting;
use Illuminate\Support\Facades\Storage;

/**
 * Images of the public site that admins can replace from Filament.
 * Each slot falls back to a default photo in public/redesign/img.
 */
class SiteImages
{
    public const SETTING_KEY = 'site_images';

    /** slot => [label, default photo name] */
    public const SLOTS = [
        'home_hero' => ['Accueil · bannière principale', 'bg_4'],
        'home_quote' => ['Accueil · bannière citation', 'bg_2'],
        'home_volunteers' => ['Accueil · appel à bénévoles', 'about'],
        'about_photo' => ['À propos · photo « Qui sommes-nous ? »', 'about'],
        'hero_orphanages' => ['Bannière · Orphelinats', 'bg_5'],
        'hero_blog' => ['Bannière · Blogs', 'bg_2'],
        'hero_about' => ['Bannière · À propos de ONOH', 'bg_4'],
        'hero_joinus' => ['Bannière · Nous rejoindre', 'bg_3'],
        'hero_contact' => ['Bannière · Contact', 'bg_1'],
    ];

    /** Slots that hold several images (slideshow), with their default photos. */
    public const MULTIPLE = [
        'home_hero' => ['bg_4', 'bg_1', 'bg_5'],
    ];

    private static ?array $paths = null;

    public static function paths(): array
    {
        return self::$paths ??= (array) AppSetting::get(self::SETTING_KEY, []);
    }

    /** Uploaded paths for a slot, always as a list. */
    public static function pathsFor(string $slot): array
    {
        return array_values(array_filter((array) (self::paths()[$slot] ?? [])));
    }

    /** URL of the (first) uploaded image for this slot, or null to use the default photo. */
    public static function url(string $slot): ?string
    {
        $path = self::pathsFor($slot)[0] ?? null;

        return $path ? Storage::disk('public')->url($path) : null;
    }

    /**
     * Slides of a multiple slot: uploaded images if any, otherwise the default photos.
     * Each slide is ['url' => string] or ['photo' => default photo name].
     */
    public static function slides(string $slot): array
    {
        $paths = self::pathsFor($slot);

        if ($paths) {
            return array_map(fn ($path) => ['url' => Storage::disk('public')->url($path)], $paths);
        }

        return array_map(fn ($photo) => ['photo' => $photo], self::MULTIPLE[$slot] ?? [self::defaultPhoto($slot)]);
    }

    public static function defaultPhoto(string $slot): string
    {
        return self::SLOTS[$slot][1] ?? 'bg_1';
    }

    /**
     * Default photo used when a record has no image (or its file is missing),
     * varied by record id so that lists don't repeat the same picture.
     */
    public static function fallbackUrl(int $seed, int $width = 640): string
    {
        $photos = ['bg_1', 'bg_2', 'bg_3', 'bg_4', 'bg_5', 'about'];

        return asset('redesign/img/' . $photos[$seed % count($photos)] . "-{$width}.webp");
    }

    public static function flush(): void
    {
        self::$paths = null;
    }
}
