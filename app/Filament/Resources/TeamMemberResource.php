<?php

namespace App\Filament\Resources;

use App\Enums\UserRoleEnum;
use App\Filament\Resources\TeamMemberResource\Pages;
use App\Models\TeamMember;
use App\Models\User;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class TeamMemberResource extends Resource
{
    protected static ?string $model = TeamMember::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    protected static ?string $navigationGroup = 'Site public';
    protected static ?string $navigationLabel = 'Équipe';
    protected static ?string $label = 'Membre de l\'équipe';
    protected static ?string $pluralLabel = 'Équipe';

    public static function canAccess(): bool
    {
        /** @var User $user */
        $user = Auth::user();

        return $user->hasRole([UserRoleEnum::ADMIN->value, UserRoleEnum::SUPER_ADMIN->value]);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->columns(3)
            ->schema([
                Section::make('Membre')
                    ->columnSpan(2)
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')
                            ->label('Nom complet')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('role')
                            ->label('Rôle')
                            ->placeholder('Ex. Responsable communication')
                            ->maxLength(255),
                        Textarea::make('bio')
                            ->label('Présentation courte')
                            ->rows(3)
                            ->columnSpanFull(),
                    ]),
                Section::make('Photo')
                    ->columnSpan(1)
                    ->schema([
                        SpatieMediaLibraryFileUpload::make('photo')
                            ->collection('photo')
                            ->hiddenLabel()
                            ->image()
                            ->imageEditor()
                            ->imageCropAspectRatio('4:5')
                            ->imageResizeTargetWidth('800')
                            ->imageResizeTargetHeight('1000')
                            ->maxSize(5120)
                            ->helperText('Format portrait (4:5) conseillé.'),
                        Toggle::make('is_visible')
                            ->label('Afficher sur le site')
                            ->default(true),
                    ]),
                Section::make('Réseaux sociaux')
                    ->description('Liens affichés sur la photo du membre.')
                    ->columnSpan(2)
                    ->columns(2)
                    ->schema([
                        TextInput::make('facebook')->label('Facebook')->url()->prefixIcon('heroicon-o-link'),
                        TextInput::make('instagram')->label('Instagram')->url()->prefixIcon('heroicon-o-link'),
                        TextInput::make('linkedin')->label('LinkedIn')->url()->prefixIcon('heroicon-o-link'),
                        TextInput::make('twitter')->label('X (Twitter)')->url()->prefixIcon('heroicon-o-link'),
                        TextInput::make('whatsapp')->label('WhatsApp')->tel()->placeholder('+237 6 XX XX XX XX')->prefixIcon('heroicon-o-phone'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->reorderable('sort_order')
            ->defaultSort('sort_order')
            ->columns([
                SpatieMediaLibraryImageColumn::make('photo')
                    ->collection('photo')
                    ->label('')
                    ->height(56)
                    ->width(45),
                TextColumn::make('name')
                    ->label('Nom')
                    ->searchable(),
                TextColumn::make('role')
                    ->label('Rôle')
                    ->searchable(),
                ToggleColumn::make('is_visible')
                    ->label('Affiché'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTeamMembers::route('/'),
            'create' => Pages\CreateTeamMember::route('/create'),
            'edit' => Pages\EditTeamMember::route('/{record}/edit'),
        ];
    }
}
