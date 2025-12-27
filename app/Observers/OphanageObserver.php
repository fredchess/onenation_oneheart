<?php

namespace App\Observers;

use App\Models\Orphanage;

class OphanageObserver
{
    public function creating(Orphanage $orphanage)
    {
        if ($orphanage->data_identity != null && $orphanage->data_identity['name'] != null && $orphanage->name == null) {
            $orphanage->name = $orphanage->data_identity['name'];
        }
    }
    /**
     * Handle the Orphanage "created" event.
     */
    public function created(Orphanage $orphanage): void
    {
        //
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
