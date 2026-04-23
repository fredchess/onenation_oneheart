<?php

namespace App\Filament\Resources;

use App\Enums\PaymentStatus;
use App\Filament\Resources\VersementResource\Pages;
use App\Models\Orphanage;
use App\Models\Versement;
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
                    ->helperText('Numéro Mobile Money de l\'orphelinat.'),

                Select::make('operator')
                    ->label('Opérateur')
                    ->options([
                        'CM_OM' => 'Orange Money (CM_OM)',
                        'CM_MOMO' => 'MTN Mobile Money (CM_MOMO)',
                    ])
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
            ]);
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
