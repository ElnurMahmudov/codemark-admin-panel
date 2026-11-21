<?php

namespace App\Filament\Pages;

use App\Models\MainSettings;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Awcodes\Curator\Components\Forms\CuratorPicker;
use App\Filament\Traits\HasSeoFields;

class MainSettingsPage extends Page
{
    use HasSeoFields;

    protected static ?string $navigationLabel = 'General Settings';
    protected static ?string $slug = 'main-settings';
    protected static ?int $navigationSort = 3;

    public static function canAccess(): bool
    {
        $user = auth('admin')->user();
        
        if (!$user) {
            return false;
        }

        return $user->hasPermission('page_main_settings_page');
    }

    public static function canView(): bool
    {
        return static::canAccess();
    }

    public static function getNavigationGroup(): string|\UnitEnum|null
    {
        return 'Settings';
    }

    public static function getNavigationIcon(): string|\BackedEnum|null
    {
        return 'heroicon-o-cog-6-tooth';
    }

    public function getTitle(): string|\Illuminate\Contracts\Support\Htmlable
    {
        return 'General Settings';
    }

    public function getView(): string
    {
        return 'filament.pages.main-settings-page';
    }

    public ?array $data = [];

    public function mount(): void
    {
        $settings = MainSettings::first() ?? new MainSettings();

        $this->form->fill($settings->toArray());
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Media')
                    ->description('Main media files for the site')
                    ->icon('heroicon-o-photo')
                    ->schema([
                        CuratorPicker::make('main_image')
                            ->label('Main Image')
                            ->buttonLabel('Select Image')
                            ->relationship('main_image', 'id')
                            ->columnSpanFull(),

                        CuratorPicker::make('logo')
                            ->label('Logo')
                            ->buttonLabel('Select Logo')
                            ->relationship('logo', 'id'),

                        CuratorPicker::make('favicon')
                            ->label('Favicon')
                            ->buttonLabel('Select Favicon')
                            ->relationship('favicon', 'id'),
                    ])
                    ->columns(2),

                Section::make('General Information')
                    ->description('General information for the site')
                    ->icon('heroicon-o-globe-alt')
                    ->schema([
                        TextInput::make('site_title')
                            ->label('Site title')
                            ->placeholder('Enter your site title')
                            ->maxLength(255)
                            ->columnSpanFull(),

                        TextInput::make('footer_text')
                            ->label('Footer text')
                            ->placeholder('Enter the footer text')
                            ->maxLength(255),

                        TextInput::make('copyright')
                            ->label('Copyright')
                            ->placeholder('Enter copyright information')
                            ->maxLength(255),
                    ])
                    ->columns(2),

                ...static::getSeoFields(),
            ])
            ->statePath('data');
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('save')
                ->label('Save')
                ->submit('save'),
        ];
    }

    public function save(): void
    {
        $data = $this->form->getState();

        $settings = MainSettings::first();

        if ($settings) {
            $settings->update($data);
        } else {
            MainSettings::create($data);
        }

        Notification::make()
            ->title('Settings saved successfully!')
            ->success()
            ->send();
    }
}