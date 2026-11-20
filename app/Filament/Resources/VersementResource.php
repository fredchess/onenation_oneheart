<?php

namespace App\Filament\Resources;

use App\Enums\PaymentStatus;
use App\Filament\Resources\VersementResource\Pages;
use App\Models\Orphanage;
use App\Models\Versement;
use App\Support\MobileMoneyNumber;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\Action;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class VersementResource extends Resource
{
    protected static ?string $model = Versement::class;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';
    protected static ?string $label = 'Versement';
    protected static ?string $pluralLabel = 'Versements';
    protected static ?int $navigationSort = 5;

    /**
     * Les versements déplacent de l'argent réel : réservé aux administrateurs.
     */
    public static function canAccess(): bool
    {
        /** @var \App\Models\User|null $user */
        $user = \Illuminate\Support\Facades\Auth::user();

        return (bool) $user?->hasRole([\App\Enums\UserRoleEnum::ADMIN->value, \App\Enums\UserRoleEnum::SUPER_ADMIN->value]);
    }

    public static function canCreate(): bool
    {
        return static::canAccess();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('orphanage_id')
                    ->label('Orphelinat')
                    ->options(Orphanage::query()->pluck('name', 'id'))
                    ->searchable()
                    ->required()
                    ->live()
                    ->afterStateUpdated(function (?int $state, Set $set, Get $get) {
                        if (! $state) {
                            return;
                        }
                        $orphanage = Orphanage::find($state);
                        if (! $orphanage) {
                            return;
                        }
                        $set('phone', $orphanage->data_financial_infos['om_momo'] ?? null);
                        $set('operator', $orphanage->data_financial_infos['payout_operator'] ?? null);
                        if ($get('source') === 'orphanage' || ! $get('source')) {
                            $set('amount', $orphanage->getAvailableDonationAmount());
                        }
                    }),

                Select::make('source')
                    ->label('Source des fonds')
                    ->options([
                        'orphanage' => "Fonds prévus pour l'orphelinat",
                        'unassigned' => 'Fonds sans orphelinat attribué',
                    ])
                    ->default('orphanage')
                    ->required()
                    ->live()
                    ->afterStateUpdated(function ($state, Set $set, Get $get) {
                        if ($state === 'unassigned') {
                            $set('amount', Versement::getUnassignedAvailableAmount());
                        } else {
                            $orphanageId = $get('orphanage_id');
                            if ($orphanageId) {
                                $orphanage = Orphanage::find($orphanageId);
                                if ($orphanage) {
                                    $set('amount', $orphanage->getAvailableDonationAmount());
                                }
                            }
                        }
                    }),

                TextInput::make('amount')
                    ->label('Montant (XAF)')
                    ->numeric()
                    ->minValue(1)
                    ->required()
                    ->helperText(function (Get $get) {
                        $source = $get('source');
                        if ($source === 'unassigned') {
                            $available = Versement::getUnassignedAvailableAmount();
                            return 'Fonds disponibles (non attribués) : ' . number_format($available, 0, ',', ' ') . ' XAF';
                        }
                        $orphanageId = $get('orphanage_id');
                        if (! $orphanageId) {
                            return "Sélectionnez un orphelinat pour voir les fonds disponibles.";
                        }
                        $orphanage = Orphanage::find($orphanageId);
                        if (! $orphanage) {
                            return null;
                        }
                        $available = $orphanage->getAvailableDonationAmount();
                        return "Fonds disponibles (orphelinat) : " . number_format($available, 0, ',', ' ') . ' XAF';
                    })
                    ->rules([
                        function (Get $get) {
                            return function (string $attribute, $value, \Closure $fail) use ($get) {
                                $source = $get('source');
                                if ($source === 'unassigned') {
                                    $available = Versement::getUnassignedAvailableAmount();
                                } else {
                                    $orphanageId = $get('orphanage_id');
                                    if (! $orphanageId) {
                                        return;
                                    }
                                    $orphanage = Orphanage::find($orphanageId);
                                    if (! $orphanage) {
                                        return;
                                    }
                                    $available = $orphanage->getAvailableDonationAmount();
                                }
                                if ((float) $value > $available) {
                                    $fail("Le montant ne peut pas dépasser les fonds disponibles ({$available} XAF).");
                                }
                            };
                        },
                    ]),

                TextInput::make('phone')
                    ->label('Numéro de téléphone (MoMo)')
                    ->required()
                    ->tel()
                    ->rules([fn (Get $get) => MobileMoneyNumber::rule(fn () => $get('operator'))])
                    ->helperText('Numéro Mobile Money de l\'orphelinat.'),

                Select::make('operator')
                    ->label('Opérateur')
                    ->options(MobileMoneyNumber::OPERATORS)
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('orphanage.name')
                    ->label('Orphelinat')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('amount')
                    ->label('Montant')
                    ->money('XAF')
                    ->sortable(),

                TextColumn::make('payment_status')
                    ->label('Statut')
                    ->badge()
                    ->color(fn (PaymentStatus $state) => $state->color())
                    ->formatStateUsing(fn (PaymentStatus $state) => $state->label()),

                TextColumn::make('datas')
                    ->label('Source')
                    ->getStateUsing(fn (Versement $record) => $record->datas['source'] ?? 'orphanage')
                    ->badge()
                    ->formatStateUsing(fn (string $state) => $state === 'unassigned' ? 'Non attribués' : 'Orphelinat')
                    ->color(fn (string $state) => $state === 'unassigned' ? 'info' : 'gray'),

                TextColumn::make('phone')
                    ->label('Numéro')
                    ->getStateUsing(fn (Versement $record) => $record->datas['phone'] ?? null)
                    ->toggleable(),

                TextColumn::make('detail')
                    ->label('Détail')
                    ->getStateUsing(fn (Versement $record) => ($record->datas['needs_check'] ?? false)
                        ? 'À vérifier sur MyCoolPay — ' . ($record->datas['error'] ?? '')
                        : ($record->datas['error'] ?? $record->datas['mcp_transaction_message'] ?? null))
                    ->color(fn (Versement $record) => ($record->datas['needs_check'] ?? false) ? 'warning' : 'gray')
                    ->limit(60)
                    ->tooltip(fn (Versement $record) => $record->datas['error'] ?? null)
                    ->toggleable(),

                TextColumn::make('initiatedBy.name')
                    ->label('Initié par')
                    ->default('Automatique'),

                TextColumn::make('created_at')
                    ->label('Date')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                //
            ])
            ->actions([
                // Pour les versements restés en attente (pas de callback, timeout) :
                // à utiliser après vérification sur le tableau de bord MyCoolPay.
                Action::make('mark_success')
                    ->label('Marquer réussi')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (Versement $record) => $record->payment_status === PaymentStatus::PENDING)
                    ->requiresConfirmation()
                    ->modalDescription('À faire uniquement après avoir vérifié sur MyCoolPay que l\'argent a bien été envoyé.')
                    ->action(fn (Versement $record) => static::resolveManually($record, PaymentStatus::SUCCESS)),
                Action::make('mark_failed')
                    ->label('Marquer échoué')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn (Versement $record) => $record->payment_status === PaymentStatus::PENDING)
                    ->requiresConfirmation()
                    ->modalDescription('Les fonds redeviendront disponibles pour un nouveau versement. À faire uniquement après avoir vérifié sur MyCoolPay que rien n\'a été envoyé.')
                    ->action(fn (Versement $record) => static::resolveManually($record, PaymentStatus::FAILED)),
            ]);
    }

    protected static function resolveManually(Versement $record, PaymentStatus $status): void
    {
        $datas = $record->datas ?? [];
        unset($datas['needs_check']);
        $datas['manual_resolution'] = [
            'status' => $status->value,
            'by' => Auth::id(),
            'at' => now()->toISOString(),
        ];

        $record->datas = $datas;
        $record->payment_status = $status;
        $record->save();

        Notification::make()->success()->title('Versement mis à jour : ' . $status->label())->send();
    }

    public static function notifyPayoutResult(Versement $versement): void
    {
        $notification = match (true) {
            $versement->payment_status === PaymentStatus::SUCCESS => Notification::make()
                ->success()
                ->title('Versement effectué'),
            $versement->payment_status === PaymentStatus::FAILED => Notification::make()
                ->danger()
                ->title('Versement refusé')
                ->body($versement->datas['error'] ?? null)
                ->persistent(),
            (bool) ($versement->datas['needs_check'] ?? false) => Notification::make()
                ->warning()
                ->title('Résultat incertain')
                ->body('MyCoolPay n\'a pas répondu. Le versement reste en attente : vérifiez sur MyCoolPay avant toute nouvelle tentative.')
                ->persistent(),
            default => Notification::make()
                ->success()
                ->title('Versement initié')
                ->body('Accepté par MyCoolPay ; le statut sera mis à jour à réception de la confirmation.'),
        };

        $notification->send();
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListVersements::route('/'),
        ];
    }

    public static function canEdit(\Illuminate\Database\Eloquent\Model $record): bool
    {
        return false;
    }

    public static function canDelete(\Illuminate\Database\Eloquent\Model $record): bool
    {
        return false;
    }
}
