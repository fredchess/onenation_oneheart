<?php

namespace App\Observers;

use App\Models\Orphanage;
use App\Support\GoogleMapsEmbed;
use App\Support\OrphanageMap;
use Illuminate\Support\Str;

class OrphanageObserver
{
    /**
     * Fills the map coordinates from the Google Maps embed when it changes,
     * unless the coordinates were typed by hand in the same save.
     */
    public function saving(Orphanage $orphanage): void
    {
        $embed = $orphanage->data_address['google_address'] ?? null;
        $embedChanged = $embed !== ($orphanage->getOriginal('data_address')['google_address'] ?? null);
        $typedByHand = $orphanage->isDirty('location') && $orphanage->coordinates();

        if ($typedByHand || (! $embedChanged && $orphanage->coordinates())) {
            return;
        }

        if ($point = GoogleMapsEmbed::coordinates($embed)) {
            $orphanage->location = $point;
        }
    }

    /**
     * Handle the Orphanage "created" event.
     */
    public function created(Orphanage $orphanage): void
    {
        $orphanage->update([
            'slug' => Str::slug($orphanage->name, language: 'fr')
        ]);
    }

    /**
     * Handle the Orphanage "updated" event.
     */
    public function updated(Orphanage $orphanage): void
    {
        OrphanageMap::forget();
    }

    /**
     * Handle the Orphanage "deleted" event.
     */
    public function deleted(Orphanage $orphanage): void
    {
        OrphanageMap::forget();
    }

    /**
     * Handle the Orphanage "restored" event.
     */
    public function restored(Orphanage $orphanage): void
    {
        //
    }

    /**
     * Handle the Orphanage "force deleted" event.
     */
    public function forceDeleted(Orphanage $orphanage): void
    {
        //
    }
}
