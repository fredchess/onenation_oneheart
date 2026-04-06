<?php

namespace App\Models;

use App\Observers\OrphanageObserver;
use CyrildeWit\EloquentViewable\Contracts\Viewable;
use CyrildeWit\EloquentViewable\InteractsWithViews;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Searchable\Searchable as SpatieSearchable;
use Spatie\Searchable\SearchResult;

#[ObservedBy(OrphanageObserver::class)]
class Orphanage extends Model implements HasMedia, SpatieSearchable, Viewable
{
    use HasFactory, InteractsWithMedia, InteractsWithViews, \Laravel\Scout\Searchable;

    protected $fillable = [
        'name',
        'data_identity',
        'status',
        'slug',
        'data',
        'data_identity_promoter',
        'data_address',
        'data_financial_infos',
        'data_stats',
        'data_education',
        'data_needs',
        'data_projects',
        'city_id',
        'responsable_id',
        'run_at',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'datas' => 'array',
        'data_identity' => 'array',
        'data_identity_promoter' => 'array',
        'data_address' => 'array',
        'data_financial_infos' => 'array',
        'data_stats' => 'array',
        'data_education' => 'array',
        'data_needs' => 'array',
        'data_projects' => 'array',
    ];

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('profile_images')->singleFile();
    }

    public function getSearchResult(): SearchResult
    {
        $url = route('public.orphanages.details', ['orphanage_slug' => $this->slug]);

        return new SearchResult(
            $this,
            $this->name,
            $url
        );
    }

    public function shouldBeSearchable(): bool
    {
        return (int) $this->status === 1;
    }

    public function searchableAs(): string
    {
        return 'orphanages';
    }

    public function toSearchableArray(): array
    {
        if (config('scout.driver') === 'typesense') {
            return [
                'id' => (string) $this->getKey(),
                'name' => $this->name ?? '',
                'localisation' => $this->data_address['localisation'] ?? '',
                'city_name' => $this->city?->name ?? '',
                'status' => (int) $this->status,
                'created_at' => $this->created_at?->timestamp ?? time(),
            ];
        }

        return [
            'name' => $this->name ?? '',
            'data_address->localisation' => $this->data_address['localisation'] ?? '',
        ];
    }

    public function typesenseCollectionSchema(): array
    {
        return [
            'name' => $this->searchableAs(),
            'fields' => [
                [
                    'name' => 'id',
                    'type' => 'string',
                ],
                [
                    'name' => 'name',
                    'type' => 'string',
                ],
                [
                    'name' => 'localisation',
                    'type' => 'string',
                    'optional' => true,
                ],
                [
                    'name' => 'city_name',
                    'type' => 'string',
                    'optional' => true,
                ],
                [
                    'name' => 'status',
                    'type' => 'int32',
                ],
                [
                    'name' => 'created_at',
                    'type' => 'int64',
                ],
            ],
            'default_sorting_field' => 'created_at',
        ];
    }

    public function typesenseSearchParameters(): array
    {
        return [
            'query_by' => 'name,localisation,city_name',
            'prioritize_exact_match' => false,
        ];
    }

    public function tags()
    {
        return $this->morphToMany(Tag::class, 'taggable');
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function dons()
    {
        return $this->hasMany(Donation::class);
    }

    public function responsable(): BelongsTo
    {
        return $this->belongsTo(User::class, 'responsable_id');
    }

    public function donations(): HasMany
    {
        return $this->hasMany(Donation::class);
    }

    public function project_categories(): BelongsToMany
    {
        return $this->belongsToMany(ProjectCategory::class);
    }
}
