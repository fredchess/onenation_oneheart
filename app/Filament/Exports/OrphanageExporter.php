<?php

namespace App\Filament\Exports;

use App\Models\Orphanage;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class OrphanageExporter extends Exporter
{
    protected static ?string $model = Orphanage::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id')
                ->label('ID'),
            ExportColumn::make('name'),
            ExportColumn::make('slug'),
            ExportColumn::make('status'),
            ExportColumn::make('city_id'),
            ExportColumn::make('location'),
            ExportColumn::make('data_identity_promoter'),
            ExportColumn::make('data_address'),
            ExportColumn::make('data_financial_infos'),
            ExportColumn::make('data_needs'),
            ExportColumn::make('responsable.name'),
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
