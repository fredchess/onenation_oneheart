<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class TeamMember extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected $fillable = [
        'name',
        'role',
        'bio',
        'facebook',
        'instagram',
        'linkedin',
        'twitter',
        'whatsapp',
        'is_visible',
        'sort_order',
    ];

    protected $casts = [
        'is_visible' => 'boolean',
    ];

    /**
     * Social networks shown on the public team card, keyed by icon name.
     */
    public const SOCIALS = [
        'facebook' => 'Facebook',
        'instagram' => 'Instagram',
        'linkedin' => 'LinkedIn',
        'twitter' => 'X (Twitter)',
        'whatsapp' => 'WhatsApp',
    ];

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('photo')->singleFile();
    }

    public function scopeVisible(Builder $query): Builder
    {
        return $query->where('is_visible', true)->orderBy('sort_order')->orderBy('id');
    }

    /**
     * Filled social links as [network => url]; a WhatsApp number becomes a wa.me link.
     */
    public function socialLinks(): array
    {
        $links = [];

        foreach (array_keys(self::SOCIALS) as $network) {
            $value = trim((string) $this->{$network});

            if ($value === '') {
                continue;
            }

            if ($network === 'whatsapp' && ! str_starts_with($value, 'http')) {
                $value = 'https://wa.me/' . preg_replace('/\D+/', '', $value);
            }

            $links[$network] = $value;
        }

        return $links;
    }
}
