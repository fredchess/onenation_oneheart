<?php

namespace App\Console\Commands;

use App\Models\Orphanage;
use App\Support\GoogleMapsEmbed;
use App\Support\OrphanageMap;
use Illuminate\Console\Command;

class ExtractOrphanageCoordinates extends Command
{
    protected $signature = 'orphanages:extract-coordinates
                            {--force : Recalcule aussi les orphelinats qui ont déjà des coordonnées}
                            {--dry-run : Affiche le résultat sans rien enregistrer}';

    protected $description = 'Remplit les coordonnées (carte) des orphelinats à partir de leur iframe Google Maps';

    public function handle(): int
    {
        $rows = [];
        $updated = 0;

        foreach (Orphanage::with('city')->orderBy('name')->get() as $orphanage) {
            $current = $orphanage->coordinates();

            if ($current && ! $this->option('force')) {
                $rows[] = [$orphanage->name, $orphanage->city?->name, $current['lat'], $current['lng'], 'déjà renseigné'];

                continue;
            }

            $point = GoogleMapsEmbed::coordinates($orphanage->data_address['google_address'] ?? null);

            if (! $point) {
                $rows[] = [$orphanage->name, $orphanage->city?->name, '—', '—', '<comment>introuvable, à saisir dans l\'admin</comment>'];

                continue;
            }

            if (! $this->option('dry-run')) {
                $orphanage->location = $point;
                $orphanage->saveQuietly();
            }

            $updated++;
            $rows[] = [$orphanage->name, $orphanage->city?->name, $point['lat'], $point['lng'], '<info>extrait</info>'];
        }

        OrphanageMap::forget();

        $this->table(['Orphelinat', 'Ville', 'Latitude', 'Longitude', 'Statut'], $rows);
        $this->info(($this->option('dry-run') ? 'À mettre à jour : ' : 'Mis à jour : ') . $updated);

        return self::SUCCESS;
    }
}
