<?php

namespace App\Filament\Pages;

use App\Models\AppSetting;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class Settings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';
    protected static ?string $navigationLabel = 'Paramètres';
    protected static ?string $title = 'Paramètres';
    protected static string $view = 'filament.pages.settings';
    protected static ?int $navigationSort = 99;

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'auto_payout_enabled' => (bool) AppSetting::get('auto_payout_enabled', false),
            'auto_payout_interval' => AppSetting::get('auto_payout_interval', 'monthly'),
            'auto_payout_day_of_week' => AppSetting::get('auto_payout_day_of_week', 1),
            'auto_payout_day_of_month' => AppSetting::get('auto_payout_day_of_month', 1),
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Transferts automatiques')
                    ->description('Configurer l\'envoi automatique des fonds collectés vers les orphelinats.')
                    ->schema([
                        Toggle::make('auto_payout_enabled')
                            ->label('Activer l\'envoi automatique')
                            ->live(),

                        Select::make('auto_payout_interval')
                            ->label('Intervalle d\'envoi')
                            ->options([
                                'weekly' => 'Chaque semaine',
                                'monthly' => 'Chaque mois',
                            ])
                            ->default('monthly')
                            ->live()
                            ->visible(fn ($get) => $get('auto_payout_enabled')),

                        Select::make('auto_payout_day_of_week')
                            ->label('Jour de la semaine')
                            ->options([
                                0 => 'Dimanche',
                                1 => 'Lundi',
                                2 => 'Mardi',
                                3 => 'Mercredi',
                                4 => 'Jeudi',
                                5 => 'Vendredi',
                                6 => 'Samedi',
                            ])
                            ->default(1)
                            ->visible(fn ($get) => $get('auto_payout_enabled') && $get('auto_payout_interval') === 'weekly'),

                        TextInput::make('auto_payout_day_of_month')
                            ->label('Jour du mois')
                            ->numeric()
                            ->minValue(1)
                            ->maxValue(28)
                            ->default(1)
                            ->helperText('Entre 1 et 28.')
                            ->visible(fn ($get) => $get('auto_payout_enabled') && $get('auto_payout_interval') === 'monthly'),
                    ]),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();

        AppSetting::set('auto_payout_enabled', (bool) ($data['auto_payout_enabled'] ?? false));
        AppSetting::set('auto_payout_interval', $data['auto_payout_interval'] ?? 'monthly');
        AppSetting::set('auto_payout_day_of_week', (int) ($data['auto_payout_day_of_week'] ?? 1));
        AppSetting::set('auto_payout_day_of_month', (int) ($data['auto_payout_day_of_month'] ?? 1));

        Notification::make()
            ->title('Paramètres sauvegardés')
            ->success()
            ->send();
    }
}
