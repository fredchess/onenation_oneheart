<?php

namespace App\Models;

use CyrildeWit\EloquentViewable\Contracts\Viewable;
use CyrildeWit\EloquentViewable\InteractsWithViews;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Searchable\Searchable;
use Spatie\Searchable\SearchResult;

class Blog extends Model implements Viewable, HasMedia, Searchable
{
    use HasFactory, InteractsWithViews, InteractsWithMedia;

    protected $fillable = [
      'name',
      'slug',
      'status',
      'datas',  
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'datas' => 'array',
    ];

    public function getSearchResult(): SearchResult
    {
        $url = route('public.blogs.details', ["blog_slug" => $this->slug]);

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
}
