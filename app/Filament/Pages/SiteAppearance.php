<?php

namespace App\Filament\Pages;

use App\Enums\UserRoleEnum;
use App\Models\AppSetting;
use App\Models\User;
use App\Support\SiteImages;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\ToggleButtons;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class SiteAppearance extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-photo';
    protected static ?string $navigationGroup = 'Site public';
    protected static ?string $navigationLabel = 'Images et affichage';
    protected static ?string $title = 'Images et affichage du site';
    protected static string $view = 'filament.pages.settings';

    public ?array $data = [];

    public static function canAccess(): bool
    {
        /** @var User $user */
        $user = Auth::user();

        return $user->hasRole([UserRoleEnum::ADMIN->value, UserRoleEnum::SUPER_ADMIN->value]);
    }

    public function mount(): void
    {
        $this->form->fill([
            'team_display' => AppSetting::get('team_display', 'grid'),
            'images' => collect(SiteImages::SLOTS)
                ->mapWithKeys(fn ($slot, $key) => [$key => array_key_exists($key, SiteImages::MULTIPLE) ? SiteImages::pathsFor($key) : (SiteImages::pathsFor($key)[0] ?? null)])
                ->all(),
        ]);
    }

    public function form(Form $form): Form
    {
        $uploads = collect(SiteImages::SLOTS)->map(function ($slot, $key) {
            $field = FileUpload::make("images.{$key}")
                ->label($slot[0])
                ->image()
                ->disk('public')
                ->directory('site')
                ->imageResizeMode('contain')
                ->imageResizeTargetWidth('1920')
                ->imageResizeTargetHeight('1920')
                ->imageResizeUpscale(false)
                ->maxSize(8192)
                ->helperText('Laisser vide pour garder la photo par défaut.');

            if (array_key_exists($key, SiteImages::MULTIPLE)) {
                $field->multiple()
                    ->reorderable()
                    ->appendFiles()
                    ->maxFiles(6)
                    ->panelLayout('grid')
                    ->columnSpanFull()
                    ->helperText('Jusqu\'à 6 images, qui défilent dans l\'ordre choisi (glisser pour réordonner). Laisser vide pour garder les photos par défaut.');
            }

            return $field;
        })->values()->all();

        return $form
            ->schema([
                Section::make('Équipe')
                    ->description('Présentation des membres sur la page « À propos de ONOH ».')
                    ->schema([
                        ToggleButtons::make('team_display')
                            ->label('Affichage des membres')
                            ->options([
                                'grid' => 'Grille',
                                'carousel' => 'Carrousel',
                            ])
                            ->icons([
                                'grid' => 'heroicon-o-squares-2x2',
                                'carousel' => 'heroicon-o-arrows-right-left',
                            ])
                            ->inline()
                            ->required()
                            ->helperText('Grille : 8 membres puis « Voir toute l\'équipe ». Carrousel : une rangée qui défile.'),
                    ]),
                Section::make('Images du site')
                    ->description('Bannières et photos des pages publiques. Format paysage conseillé, au moins 1600 px de large.')
                    ->columns(['md' => 2, 'xl' => 3])
                    ->schema($uploads),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();
        $previous = collect(SiteImages::paths())->flatten()->filter()->all();
        $images = collect(array_intersect_key($data['images'] ?? [], SiteImages::SLOTS))
            ->map(fn ($value, $slot) => array_key_exists($slot, SiteImages::MULTIPLE) ? array_values(array_filter((array) $value)) : $value)
            ->filter()
            ->all();

        // Supprime les fichiers remplacés ou retirés
        $kept = collect($images)->flatten()->all();
        foreach (array_diff($previous, $kept) as $path) {
            Storage::disk('public')->delete($path);
        }

        AppSetting::set(SiteImages::SETTING_KEY, $images);
        AppSetting::set('team_display', $data['team_display'] ?? 'grid');
        SiteImages::flush();

        Notification::make()
            ->title('Apparence du site enregistrée')
            ->success()
            ->send();
    }
}
