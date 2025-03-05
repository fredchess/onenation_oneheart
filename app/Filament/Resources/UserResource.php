<?php

namespace App\Filament\Resources;

use App\Enums\UserRoleEnum;
use App\Filament\Exports\OrphanageExporter;
use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use Filament\Actions\ExportAction;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-user';
    protected static ?string $navigationGroup = 'Administration';
    public static ?string $label = 'Utilisateur';
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
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('email')
                    ->email()
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('datas.tel')
                    ->required(),
                Forms\Components\TextInput::make('datas.pays')
                    ->required(),
                Forms\Components\TextInput::make('datas.ville')
                    ->required(),
                Forms\Components\TextInput::make('password')
                    ->password()
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),
                TextColumn::make('datas.tel')->label('Tel'),
                TextColumn::make('datas.pays')
                    ->getStateUsing(function ($record) {
                        return $record->datas['pays'] .' - '. $record->datas['ville'];
                    })
                    ->label('Pays/Ville'),
                TextColumn::make('datas.preferences')->label('Compétences'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->label("Date d'ajout")
                    ->toggleable(isToggledHiddenByDefault: false)->date(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])->modifyQueryUsing(function (Builder $query) {
                $query->whereHas('roles', function (Builder $query) {
                    $query->where('name', UserRoleEnum::USER->value)
                        ->orWhere('name', UserRoleEnum::ADMIN->value);
                })
                ->orderBy('created_at', 'desc');
            });
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageUsers::route('/'),
        ];
    }
}
