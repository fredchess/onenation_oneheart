<?php

namespace App\Observers;

use App\Models\Orphanage;
use Illuminate\Support\Str;

class OrphanageObserver
{
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
        //
    }

    /**
     * Handle the Orphanage "deleted" event.
     */
    public function deleted(Orphanage $orphanage): void
    {
        //
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
