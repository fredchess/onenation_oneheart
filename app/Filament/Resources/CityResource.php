<?php

namespace App\Filament\Resources;

use App\Enums\UserRoleEnum;
use App\Filament\Resources\CityResource\Pages;
use App\Models\City;
use App\Models\User;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CityResource extends Resource
{
    protected static ?string $model = City::class;

    protected static ?string $navigationIcon = 'heroicon-o-map-pin';
    protected static ?string $label = 'Ville';
    protected static ?string $pluralLabel = 'Villes';
    protected static ?string $navigationGroup = 'Administration';

    public static function canAccess(): bool
    {
        /** @var User $user */
        $user = Auth::user();

        return $user->hasRole([UserRoleEnum::ADMIN->value, UserRoleEnum::SUPER_ADMIN->value]);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->label('Nom')
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true),
                TextInput::make('description')
                    ->label('Description'),
                TextInput::make('country_name')
                    ->label('Pays'),
                TextInput::make('country_code')
                    ->label('Code pays')
                    ->maxLength(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query->withCount('orphanages'))
            ->columns([
                TextColumn::make('name')
                    ->label('Nom')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('country_name')
                    ->label('Pays')
                    ->toggleable(),
                TextColumn::make('orphanages_count')
                    ->label('Orphelinats')
                    ->badge()
                    ->color(fn (int $state) => $state > 0 ? 'primary' : 'gray')
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Créée le')
                    ->dateTime('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('name')
            ->actions([
                Tables\Actions\EditAction::make()
                    ->after(fn (City $record) => static::reindexOrphanages($record)),

                // Réaffecte les orphelinats vers une autre ville, puis supprime celle-ci.
                Action::make('merge')
                    ->label('Fusionner')
                    ->icon('heroicon-o-arrows-right-left')
                    ->color('warning')
                    ->visible(fn (City $record) => $record->orphanages_count > 0)
                    ->modalHeading(fn (City $record) => "Fusionner « {$record->name} »")
                    ->modalDescription(fn (City $record) => "Les {$record->orphanages_count} orphelinat(s) rattaché(s) seront déplacés vers la ville choisie, puis « {$record->name} » sera supprimée.")
                    ->form(fn (City $record) => [
                        Select::make('target_city_id')
                            ->label('Ville de destination')
                            ->options(City::query()->whereKeyNot($record->getKey())->orderBy('name')->pluck('name', 'id'))
                            ->searchable()
                            ->required(),
                    ])
                    ->action(function (City $record, array $data) {
                        $target = City::findOrFail($data['target_city_id']);

                        DB::transaction(function () use ($record, $target) {
                            // Mise à jour modèle par modèle pour déclencher observers et indexation.
                            $record->orphanages()->each(fn ($orphanage) => $orphanage->update(['city_id' => $target->id]));
                            $record->delete();
                        });

                        Notification::make()
                            ->success()
                            ->title("Ville fusionnée dans « {$target->name} »")
                            ->send();
                    }),

                Tables\Actions\DeleteAction::make()
                    ->visible(fn (City $record) => $record->orphanages_count === 0),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->action(function (Collection $records) {
                            $deletable = $records->filter(fn (City $city) => ! $city->orphanages()->exists());
                            $deletable->each->delete();

                            $skipped = $records->count() - $deletable->count();

                            Notification::make()
                                ->success()
                                ->title($deletable->count() . ' ville(s) supprimée(s)')
                                ->body($skipped > 0 ? "{$skipped} ville(s) ignorée(s) car encore rattachée(s) à des orphelinats : utilisez « Fusionner »." : null)
                                ->send();
                        }),
                ]),
            ]);
    }

    /**
     * Le nom de la ville est indexé avec les orphelinats (recherche) : on resynchronise après modification.
     */
    protected static function reindexOrphanages(City $city): void
    {
        if ($city->wasChanged('name')) {
            $city->orphanages()->searchable();
        }
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageCities::route('/'),
        ];
    }
}
