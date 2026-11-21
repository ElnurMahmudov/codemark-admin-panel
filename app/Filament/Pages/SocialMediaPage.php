<?php

namespace App\Filament\Pages;

use App\Models\ContactInformation;
use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class SocialMediaPage extends Page
{
    protected static ?string $navigationLabel = 'Social Media Links';
    protected static ?string $slug = 'social-media-links';
    protected static ?int $navigationSort = 5;

    public static function canAccess(): bool
    {
        $user = auth('admin')->user();
        
        if (!$user) {
            return false;
        }

        return $user->hasPermission('page_social_media_page');
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
        return 'heroicon-o-share';
    }

    public function getTitle(): string|\Illuminate\Contracts\Support\Htmlable
    {
        return 'Social Media Links';
    }

    public function getView(): string
    {
        return 'filament.pages.social-media-page';
    }

    public ?array $data = [];

    public function mount(): void
    {
        $social = ContactInformation::first() ?? new ContactInformation();

        $this->form->fill($social->toArray());
    }


    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Social Media Links')
                    ->description('Links to social media profiles')
                    ->icon('heroicon-o-globe-alt')
                    ->schema([
                        TextInput::make('facebook_url')
                            ->label('Facebook URL')
                            ->url() // Link formatını məcbur edir
                            ->placeholder('https://www.facebook.com/yourpage')
                            ->columnSpanFull(),

                        TextInput::make('instagram_url')
                            ->label('Instagram URL')
                            ->url()
                            ->placeholder('https://www.instagram.com/yourprofile')
                            ->columnSpanFull(),

                        TextInput::make('linkedin_url')
                            ->label('LinkedIn URL')
                            ->url()
                            ->placeholder('https://www.linkedin.com/in/yourprofile')
                            ->columnSpanFull(),

                        TextInput::make('youtube_url')
                            ->label('YouTube URL')
                            ->url()
                            ->placeholder('https://www.youtube.com/channel/yourchannel')
                            ->columnSpanFull(),

                        TextInput::make('tiktok_url')
                            ->label('TikTok URL')
                            ->url()
                            ->placeholder('https://www.tiktok.com/@yourprofile')
                            ->columnSpanFull(),
                    ]),
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

        $social = ContactInformation::first();

        if ($social) {
            $social->update($data);
        } else {
            ContactInformation::create($data);
        }

        Notification::make()
            ->title('Social media links saved successfully!')
            ->success()
            ->send();
    }
}