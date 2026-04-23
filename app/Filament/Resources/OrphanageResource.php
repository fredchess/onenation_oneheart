<?php

namespace App\Filament\Resources;

use App\Enums\PaymentStatus;
use App\Enums\UserRoleEnum;
use App\Filament\Exports\OrphanageExporter;
use App\Filament\Resources\OrphanageResource\Pages;
use App\Models\Orphanage;
use App\Models\User;
use App\Models\Versement;
use App\Services\MyCoolPayPayoutService;
use Filament\Forms;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Support\Enums\IconPosition;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ExportAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class OrphanageResource extends Resource
{
    protected static ?string $model = Orphanage::class;

    protected static ?string $navigationIcon = 'heroicon-o-home';
    protected static ?string $label = "Orphelinat";

    public static function form(Form $form): Form
    {
        /**
         * @var User $user
         */
        $user = Auth::user();

        return $form
            ->schema([
                Section::make('ID Orphelinat')
                    ->schema([
                        TextInput::make('data_identity.name')
                            ->label('Nom')
                            ->columnSpanFull(),
                        TextInput::make('data_identity.email')
                            ->label('email')
                            ->email(),
                        TextInput::make('data_identity.website')
                            ->label('Site web'),
                        TextInput::make('data_identity.arret_number')
                            ->label('Numero d\'arrete'),
                        RichEditor::make('data_identity.history')
                            ->label('Histoire')
                            ->columnSpanFull(),
                        Textarea::make('data_identity.mini_description')
                            ->label('Mini Description')
                            ->columnSpanFull(),
                        RichEditor::make('data_identity.description')
                            ->label('Description')
                            ->columnSpanFull(),
                        SpatieMediaLibraryFileUpload::make('image')
                            ->collection('profile_images')
                            ->columnSpanFull(),
                        Checkbox::make('status')
                            ->label('Rendre visible'),
                    ])
                    ->collapsible()
                    ->collapsed()
                    ->persistCollapsed()
                    ->columns(2),
                Section::make('Informations du promotteur')
                    ->schema([
                        Tabs::make('Promoteur')
                            ->tabs([
                                Tab::make('Promoteur')
                                    ->schema([
                                        Select::make('reponsable_id')
                                            ->label('Promotteur')
                                            ->relationship('responsable', 'name')
                                            ->createOptionForm([
                                                Forms\Components\TextInput::make('name')
                                                    ->required()
                                                    ->label('Nom'),
                                                Forms\Components\TextInput::make('email')
                                                    ->email()
                                                    ->required()
                                                    ->label('email'),
                                                Forms\Components\TextInput::make('password')
                                                    ->password()
                                                    ->label('Mot de passe'),
                                                TextInput::make('tel')
                                                    ->label('Telephone')
                                                    ->required()
                                                    ->tel(),
                                            ])
                                            ->options(function () {
                                                return User::query()
                                                    ->with('roles')
                                                    ->whereHas('roles', function (Builder $query) {
                                                        $query->where('name', 'responsable');
                                                    })
                                                    ->pluck('name', 'id');
                                            })
                                            ->searchable()
                                            ->preload()
                                            ->createOptionUsing(function (array $data) {
                                                $user =  User::query()->create([
                                                    'name' => $data['name'],
                                                    'email' => $data['email'],
                                                    'password' => Hash::make($data['password']),
                                                    'datas' => ['tel' => $data['tel'], 'visible' => true]
                                                ]);

                                                if (Role::where('name', 'responsable')->first() == null) Role::create(['name' => 'responsable']);

                                                $user->assignRole(['responsable']);

                                                return $user->getkey();
                                            })
                                    ])
                                    ->columns(2)
                                    ->hidden(!$user->hasRole([UserRoleEnum::ADMIN->value, UserRoleEnum::SUPER_ADMIN->value])),
                                Tab::make('Secondant')
                                    ->schema([
                                        TextInput::make('data_identity_promoter.second_name')
                                            ->label('Nom du secondant'),
                                        TextInput::make('data_identity_promoter.second_phone')
                                            ->label('Téléphone du secondant')
                                    ])
                                    ->columns(2)
                            ])
                            ->columnSpanFull()

                    ])
                    ->collapsible()
                    ->collapsed()
                    ->persistCollapsed()
                    ->columns(2),
                Section::make('Adresse de l\'orphelinat')
                    ->schema([
                        Select::make('city_id')
                            ->label('Ville')
                            ->relationship('city', 'name')
                            ->searchable()
                            ->required()
                            ->preload()
                            ->createOptionForm([
                                TextInput::make('name')
                                    ->label('Nom')
                                    ->required(),
                                TextInput::make('description'),
                                TextInput::make('country_name'),
                                TextInput::make('country_code'),
                            ]),

                        TextInput::make('data_address.google_address')
                            ->label('Adresse Google'),
                        TextInput::make('data_address.localisation')
                            ->label('Localisation')
                    ])
                    ->collapsible()
                    ->collapsed()
                    ->persistCollapsed()
                    ->columns(2),
                Section::make('Informations financières')
                    ->schema([
                        TextInput::make('bank_number'),
                        TextInput::make('om_momo')
                            ->label('Numéro Mobile Money (OM / MTN)'),
                        Select::make('payout_operator')
                            ->label('Opérateur Mobile Money')
                            ->options([
                                'CM_OM' => 'Orange Money (CM_OM)',
                                'CM_MOMO' => 'MTN Mobile Money (CM_MOMO)',
                            ]),
                    ])
                    ->statePath('data_financial_infos')
                    ->collapsible()
                    ->collapsed()
                    ->persistCollapsed()
                    ->columns(2),
                Section::make('Statistiques')
                    ->schema([
                        TextInput::make('data_stats.children_number')
                            ->numeric()
                            ->minValue(0)
                            ->label('Nombre d\'enfants'),
                        TextInput::make('data_stats.children_number_0_6')
                            ->numeric()
                            ->minValue(0)
                            ->label('Nombre d\'enfants dans la tranche 0-6 ans'),
                        TextInput::make('data_stats.children_number_7_13')
                            ->numeric()
                            ->minValue(0)
                            ->label('Nombre d\'enfants dans la tranche 7-13 ans'),
                        TextInput::make('data_stats.children_number_14_21')
                            ->numeric()
                            ->minValue(0)
                            ->label('Nombre d\'enfants dans la tranche 14-21 ans'),
                        TextInput::make('data_stats.boys_number')
                            ->numeric()
                            ->minValue(0)
                            ->label('Nombre de garçons'),
                        TextInput::make('data_stats.girls_number')
                            ->numeric()
                            ->minValue(0)
                            ->label('Nombre de filles'),

                    ])
                    ->collapsible()
                    ->collapsed()
                    ->persistCollapsed()
                    ->columns(2),
                Section::make('Education dans l\'orphelinat')
                    ->schema([
                        TextInput::make('school_children_number')
                            ->numeric()
                            ->minValue(0)
                            ->label('Nombre d\'enfants scolarisés'),
                        TextInput::make('schoolexam_children_number')
                            ->numeric()
                            ->minValue(0)
                            ->label('Nombre d\'enfants en classe d\'examen'),
                        TextInput::make('maternelle_children_number')
                            ->numeric()
                            ->minValue(0)
                            ->label('Nombre d\'enfants en maternelle'),
                        TextInput::make('primary_children_number')
                            ->numeric()
                            ->minValue(0)
                            ->label('Nombre d\'enfants en primaire'),
                        TextInput::make('college_children_number')
                            ->numeric()
                            ->minValue(0)
                            ->label('Nombre d\'enfants au collège'),
                        TextInput::make('university_children_number')
                            ->numeric()
                            ->minValue(0)
                            ->label('Nombre d\'enfants à l\'université'),
                        TextInput::make('professional_children_number')
                            ->numeric()
                            ->minValue(0)
                            ->label('Nombre d\'enfants en recherche de formation professionnelle'),
                    ])
                    ->statePath('data_education')
                    ->collapsible()
                    ->collapsed()
                    ->persistCollapsed()
                    ->columns(2),
                Section::make('Besoins de l\'orphelinat')
                    ->schema([
                        RichEditor::make('food_needs')
                            ->label('Alimentaires'),
                        RichEditor::make('health_needs')
                            ->label('Sanitaires + hygiéniques'),
                        RichEditor::make('school_needs')
                            ->label('Scolaires'),
                        RichEditor::make('clothes_needs')
                            ->label('Vestimentaires'),
                        RichEditor::make('ludic_needs')
                            ->label('Ludiques'),
                        RichEditor::make('other_needs')
                            ->label('Autres'),
                    ])
                    ->statePath('data_needs')
                    ->collapsible()
                    ->collapsed()
                    ->persistCollapsed(),
                Section::make('Projets de l\'orphelinat')
                    ->schema([
                        RichEditor::make('projects')
                            ->label('Projets')
                            ->columnSpanFull(),
                        Select::make('project_categories')
                            ->label('Projects (catégories)')
                            ->relationship('project_categories', 'name')
                            ->preload()
                            ->multiple()
                            ->createOptionForm([
                                TextInput::make('name')
                            ]),
                    ])
                    ->statePath('data_projects')
                    ->collapsible()
                    ->collapsed()
                    ->columns(2)
                    ->persistCollapsed(),
                Section::make('ONOH et l\'orphelinat')
                    ->schema([
                        RichEditor::make('data_identity.withonoh')
                            ->columnSpanFull()
                            ->label('ONOH et l\'orphelinat'),
                        DatePicker::make('run_at')
                            ->label('Dernière visite')
                    ])
                    ->collapsible()
                    ->collapsed()
                    ->columns(2)
                    ->persistCollapsed(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nom')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('visites')
                    ->label('Nb. visites')
                    ->getStateUsing(fn ($record) => views($record)->count())
                    ->sortable(query: fn (Builder $query, string $direction) => $query->orderBy('visites_count', $direction)),
                TextColumn::make('city.name'),
                TextColumn::make('total_dons_disponibles')
                    ->label('Dons disponibles')
                    ->getStateUsing(fn (Orphanage $record) => $record->getAvailableDonationAmount())
                    ->money('XAF')
                    ->sortable(query: fn (Builder $query, string $direction) => $query->orderBy('dons_disponibles_sort', $direction)),
            ])
            ->defaultSort('dons_disponibles_sort', 'desc')
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Action::make('verser')
                    ->label('Verser')
                    ->icon('heroicon-o-arrow-up-circle')
                    ->color('success')
                    ->visible(function (Orphanage $record) {
                        static $unassignedAmount = null;
                        if ($unassignedAmount === null) {
                            $unassignedAmount = Versement::getUnassignedAvailableAmount();
                        }
                        return ($record->getAvailableDonationAmount() > 0 || $unassignedAmount > 0)
                            && ! $record->hasPendingVersement();
                    })
                    ->form(function (Orphanage $record) {
                        $orphanageAvailable = $record->getAvailableDonationAmount();

                        return [
                            Select::make('source')
                                ->label('Source des fonds')
                                ->options([
                                    'orphanage' => "Fonds prévus pour l'orphelinat",
                                    'unassigned' => 'Fonds sans orphelinat attribué',
                                ])
                                ->default($orphanageAvailable > 0 ? 'orphanage' : 'unassigned')
                                ->required()
                                ->live()
                                ->afterStateUpdated(function ($state, Set $set) use ($record) {
                                    if ($state === 'unassigned') {
                                        $set('amount', Versement::getUnassignedAvailableAmount());
                                    } else {
                                        $set('amount', $record->getAvailableDonationAmount());
                                    }
                                }),

                            TextInput::make('amount')
                                ->label('Montant à verser (XAF)')
                                ->numeric()
                                ->minValue(1)
                                ->default($orphanageAvailable > 0 ? $orphanageAvailable : Versement::getUnassignedAvailableAmount())
                                ->required()
                                ->helperText(function (Get $get) use ($record) {
                                    $source = $get('source');
                                    if ($source === 'unassigned') {
                                        $available = Versement::getUnassignedAvailableAmount();
                                        return 'Fonds disponibles (non attribués) : ' . number_format($available, 0, ',', ' ') . ' XAF';
                                    }
                                    $available = $record->getAvailableDonationAmount();
                                    return "Fonds disponibles (orphelinat) : " . number_format($available, 0, ',', ' ') . ' XAF';
                                })
                                ->rules([
                                    function (Get $get) use ($record) {
                                        return function (string $attribute, $value, \Closure $fail) use ($get, $record) {
                                            $source = $get('source');
                                            $available = $source === 'unassigned'
                                                ? Versement::getUnassignedAvailableAmount()
                                                : $record->getAvailableDonationAmount();
                                            if ((float) $value > $available) {
                                                $fail("Le montant ne peut pas dépasser les fonds disponibles ({$available} XAF).");
                                            }
                                        };
                                    },
                                ]),

                            TextInput::make('phone')
                                ->label('Numéro Mobile Money')
                                ->default($record->data_financial_infos['om_momo'] ?? null)
                                ->required(),

                            Select::make('operator')
                                ->label('Opérateur')
                                ->options([
                                    'CM_OM' => 'Orange Money (CM_OM)',
                                    'CM_MOMO' => 'MTN Mobile Money (CM_MOMO)',
                                ])
                                ->default($record->data_financial_infos['payout_operator'] ?? null)
                                ->required(),
                        ];
                    })
                    ->action(function (Orphanage $record, array $data) {
                        $versement = Versement::create([
                            'orphanage_id' => $record->id,
                            'amount' => $data['amount'],
                            'payment_status' => PaymentStatus::PENDING->value,
                            'initiated_by' => Auth::id(),
                            'datas' => [
                                'phone' => $data['phone'],
                                'operator' => $data['operator'],
                                'source' => $data['source'] ?? 'orphanage',
                            ],
                        ]);

                        try {
                            $service = new MyCoolPayPayoutService();
                            $balance = $service->getBalance();

                            if ($balance < $versement->amount) {
                                $versement->payment_status = PaymentStatus::FAILED;
                                $versement->save();

                                Notification::make()
                                    ->danger()
                                    ->title('Solde MyCoolPay insuffisant')
                                    ->body('Solde disponible : ' . number_format($balance, 0, ',', ' ') . ' XAF')
                                    ->persistent()
                                    ->send();

                                return;
                            }

                            $service->payout($versement, $data['phone'], $data['operator']);

                            Notification::make()
                                ->success()
                                ->title('Versement initié')
                                ->body('Le versement a été soumis à MyCoolPay.')
                                ->send();

                        } catch (\Exception $e) {
                            $versement->payment_status = PaymentStatus::FAILED;
                            $versement->save();

                            Notification::make()
                                ->danger()
                                ->title('Erreur lors du versement')
                                ->body($e->getMessage())
                                ->persistent()
                                ->send();
                        }
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->modifyQueryUsing(function (Builder $query) {
                $resetDate = \Carbon\Carbon::parse(
                    \App\Models\AppSetting::get('payout_counter_reset_at', now()->toISOString())
                )->format('Y-m-d H:i:s');

                $viewsTable = config('eloquent-viewable.models.view.table_name', 'views');
                $orphanageClass = addslashes(Orphanage::class);

                $query->selectRaw("
                    orphanages.*,
                    COALESCE((
                        SELECT SUM(d.amount) FROM donations d
                        WHERE d.orphanage_id = orphanages.id
                          AND d.payment_status = 'success'
                          AND d.created_at > GREATEST(
                            '{$resetDate}',
                            COALESCE((
                                SELECT v.created_at FROM versements v
                                WHERE v.orphanage_id = orphanages.id
                                  AND v.payment_status = 'success'
                                ORDER BY v.created_at DESC LIMIT 1
                            ), '1970-01-01 00:00:00')
                          )
                    ), 0) as dons_disponibles_sort,
                    (
                        SELECT COUNT(*) FROM {$viewsTable}
                        WHERE viewable_type = '{$orphanageClass}'
                          AND viewable_id = orphanages.id
                    ) as visites_count
                ");

                /** @var User $user */
                $user = Auth::user();

                if ($user->hasRole([UserRoleEnum::ADMIN->value, UserRoleEnum::SUPER_ADMIN->value])) {
                    return $query;
                }

                return $query->with('responsable')
                    ->where('orphanages.responsable_id', $user->id);
            })->headerActions([
                ExportAction::make()->exporter(OrphanageExporter::class)
                    ->label('Exporter')
                    ->color('secondary')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->iconPosition(IconPosition::After),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrphanages::route('/'),
            'create' => Pages\CreateOrphanage::route('/create'),
            'edit' => Pages\EditOrphanage::route('/{record}/edit'),
        ];
    }
}
