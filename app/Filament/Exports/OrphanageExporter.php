<?php

namespace App\Filament\Exports;

use App\Models\Orphanage;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;
use Illuminate\Database\Eloquent\Builder;

class OrphanageExporter extends Exporter
{
    protected static ?string $model = Orphanage::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id')
                ->label('ID'),
            ExportColumn::make('name'),
            ExportColumn::make('data_stats1')
                ->label("Nb. d'enfants")
                ->state(function (Orphanage $orphanage) {
                    return "{$orphanage->data_stats['children_number_0_6']}, {$orphanage->data_stats['children_number_7_13']}, {$orphanage->data_stats['children_number_14_21']}" ?? 0;
                }),
            ExportColumn::make('data_stats_tranches')
                ->label("Tranches d'age")
                ->state(function (Orphanage $orphanage) {
                    return $orphanage->data_stats['children_number'] ?? 0;
                }),
            ExportColumn::make('data_stats2')
                ->label("Nb. de filles")
                ->state(function (Orphanage $orphanage) {
                    return $orphanage->data_stats['girls_number'] ?? 0;
                }),
            ExportColumn::make('data_stats3')
                ->label("Nb. de garcon")
                ->state(function (Orphanage $orphanage) {
                    return $orphanage->data_stats['boys_number'] ?? 0;
                }),
            ExportColumn::make('data_stats4')
                ->label("Scolarisés")
                ->state(function (Orphanage $orphanage) {
                    return $orphanage->data_education['school_children_number'] ?? 0;
                }),
            ExportColumn::make('data_stats5')
                ->label("Maternelle")
                ->state(function (Orphanage $orphanage) {
                    return $orphanage->data_education['maternelle_children_number'] ?? 0;
                }),
            ExportColumn::make('data_stats6')
                ->label("Primaire")
                ->state(function (Orphanage $orphanage) {
                    return $orphanage->data_education['primary_children_number'] ?? 0;
                }),
            ExportColumn::make('data_stats8')
                ->label("Secondaire")
                ->state(function (Orphanage $orphanage) {
                    return $orphanage->data_education['college_children_number'] ?? 0;
                }),
            ExportColumn::make('data_stats9')
                ->label("Secondaire")
                ->state(function (Orphanage $orphanage) {
                    return $orphanage->data_education['university_children_number'] ?? 0;
                }),
            ExportColumn::make('data_stats10')
                ->label("Secondaire")
                ->state(function (Orphanage $orphanage) {
                    return $orphanage->data_education['schollexam_children_number'] ?? 0;
                }),
            ExportColumn::make('data_stats_workers')
                ->label("Travailleurs")
                ->state(function (Orphanage $orphanage) {
                    return $orphanage->data_education['professional_children_number'] ?? 0;
                }),
            ExportColumn::make('donations_count')
                ->label('Nombre de dons')
                ->counts('donations'),
            ExportColumn::make('donations_sum_amount')
                ->label('Total dons')
                ->sum(['donations' => fn (Builder $query) => $query->where('status', 1)], 'amount')
                ->default(0),
            ExportColumn::make('created_at')
                ->label('Rajout'),
            ExportColumn::make('updated_at')
                ->label('Modification'),
            ExportColumn::make('city.name')->label('Ville'),
            ExportColumn::make('total_visits')
                ->label('Visites')
                ->state(fn (Orphanage $orphanage) => views($orphanage)->count()),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your orphanage export has completed and ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}
