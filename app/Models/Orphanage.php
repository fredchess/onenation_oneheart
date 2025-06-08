<?php

namespace App\Models;

use App\Models\Tag;
use App\Models\City;
use App\Models\Donation;
use App\Observers\OrphanageObserver;
use Spatie\MediaLibrary\HasMedia;
use Spatie\Searchable\Searchable;
use Spatie\Searchable\SearchResult;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\InteractsWithMedia;
use CyrildeWit\EloquentViewable\Contracts\Viewable;
use CyrildeWit\EloquentViewable\InteractsWithViews;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;


#[ObservedBy(OrphanageObserver::class)]
class Orphanage extends Model implements Viewable, HasMedia, Searchable
{
    use HasFactory, InteractsWithViews, InteractsWithMedia;

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
        $url = route('public.orphanages.details', ["orphanage_slug" => $this->slug]);

         return new \Spatie\Searchable\SearchResult(
            $this,
            $this->name,
            $url
         );
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

    public function responsable() : BelongsTo
    {
        return $this->belongsTo(User::class, 'responsable_id');
    }

    public function donations() : HasMany
    {
        return $this->hasMany(Donation::class);
    }

    public function project_categories() : BelongsToMany
    {
        return $this->belongsToMany(ProjectCategory::class);
    }
}
