<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Artisan;

/**
 * One-off: fills orphanages.location (map) from the Google Maps embeds already
 * stored, so the map works right after deploy. Safe to re-run by hand with
 * `php artisan orphanages:extract-coordinates`.
 */
return new class extends Migration
{
    public function up(): void
    {
        Artisan::call('orphanages:extract-coordinates');
    }

    public function down(): void
    {
        //
    }
};
