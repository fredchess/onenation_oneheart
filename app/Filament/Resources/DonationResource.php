<?php

namespace App\Filament\Resources;

use App\Enums\PaymentStatus;
use App\Enums\UserRoleEnum;
use App\Filament\Resources\DonationResource\Pages;
use App\Models\Donation;
use App\Models\User;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class DonationResource extends Resource
{
    protected static ?string $model = Donation::class;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';
    protected static ?string $navigationGroup = 'Administration';
    protected static ?string $label = 'Don';

    public static function canAccess(): bool
    {
        /** @var User $user */
        $user = Auth::user();

        return $user->hasRole([UserRoleEnum::ADMIN->value, UserRoleEnum::SUPER_ADMIN->value]);
    }

    public static function form(Form $form): Form
    {
        return $form->schema([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('datas.name')
                    ->label('Nom')
                    ->searchable(),
                TextColumn::make('datas.email')
                    ->label('Email')
                    ->searchable(),
                TextColumn::make('datas.tel')
                    ->label('Tel'),
                TextColumn::make('amount')
                    ->label('Montant')
                    ->money('XAF', locale: 'fr_FR'),
                TextColumn::make('payment_mode_label')
                    ->label('Mode de paiement'),
                TextColumn::make('orphanage.name')
                    ->label('Orphelinat')
                    ->searchable(),
                TextColumn::make('payment_status_label')
                    ->label('Statut')
                    ->badge()
                    ->color(fn (Donation $record): string => $record->payment_status?->color() ?? 'gray'),
                TextColumn::make('created_at')
                    ->label('Fait le'),
            ])
            ->filters([
                SelectFilter::make('payment_status')
                    ->label('Statut')
                    ->options([
                        PaymentStatus::PENDING->value => PaymentStatus::PENDING->label(),
                        PaymentStatus::SUCCESS->value => PaymentStatus::SUCCESS->label(),
                        PaymentStatus::FAILED->value => PaymentStatus::FAILED->label(),
                    ]),
            ])
            ->actions([
                Action::make('validate')
                    ->label('Valider le paiement')
                    ->action(function (Donation $record): void {
                        $record->payment_status = PaymentStatus::SUCCESS;
                        $record->save();
                    })
                    ->icon('heroicon-o-check-circle')
                    ->requiresConfirmation()
                    ->hidden(fn (Donation $record): bool => $record->payment_status === PaymentStatus::SUCCESS),
                Tables\Actions\DeleteAction::make()
                    ->hidden(fn (Donation $record): bool => $record->payment_status === PaymentStatus::SUCCESS),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->modifyQueryUsing(fn (Builder $query) => $query->orderBy('created_at', 'desc'));
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageDonations::route('/'),
        ];
    }
}
