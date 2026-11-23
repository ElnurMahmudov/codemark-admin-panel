<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Models\ContactInformation;
use Filament\Actions\Action;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Repeater;
use Filament\Support\Enums\Alignment;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;

class ContactInformationPage extends Page
{
    protected static ?string $navigationLabel = 'Əlaqə Məlumatları';
    protected static ?string $slug = 'contact-information';
    protected static ?int $navigationSort = 2;

    public static function canAccess(): bool
    {
        $user = auth('admin')->user();
        
        if (!$user) {
            return false;
        }

        return $user->hasPermission('page_contact_information_page');
    }

    public static function canView(): bool
    {
        return static::canAccess();
    }

    public static function getNavigationGroup(): string|\UnitEnum|null
    {
        return 'Tənzimləmələr';
    }

    public static function getNavigationIcon(): string|\BackedEnum|null
    {
        return 'heroicon-o-phone';
    }    

    // İşləyən struktur üçün əlavə edildi
    public function getTitle(): string|\Illuminate\Contracts\Support\Htmlable
    {
        return 'Əlaqə Məlumatları';
    }

    // İşləyən struktur üçün əlavə edildi
    public function getView(): string
    {
        return 'filament.pages.contact-information-page';
    }

    public ?array $data = [];

    public function mount(): void
    {
        $contactInfo = ContactInformation::first() ?? new ContactInformation();

        $this->form->fill($contactInfo->toArray());
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Contact Information')
                    ->description('Manage the contact details for the website')
                    ->icon('heroicon-o-phone')
                    ->schema([
                        Grid::make(2) 
                            ->schema([
                                Repeater::make('phones')
                                    ->label('Phone Numbers')
                                    ->addActionLabel('Add Phone')
                                    ->addActionAlignment(Alignment::Start)
                                    ->reorderableWithButtons()
                                    ->schema([
                                        TextInput::make('number')
                                            ->label('Number')
                                            ->required(),
                                    ]),

                                Repeater::make('emails')
                                    ->label('Email Addresses')
                                    ->addActionLabel('Add Email')
                                    ->reorderableWithButtons()
                                    ->addActionAlignment(Alignment::Start)
                                    ->schema([
                                        TextInput::make('email')
                                            ->label('Email')
                                            ->email()
                                            ->required(),
                                    ]),
                            ]),
                    ]),

                Section::make('Address')
                    ->description('Physical address and map information')
                    ->icon('heroicon-o-map')
                    ->schema([
                        TextInput::make('address')
                            ->label('Address')
                            ->placeholder('Enter the physical address')
                            ->columnSpanFull(),

                        TextInput::make('map_url')
                            ->label('Google Maps URL')
                            ->url()
                            ->placeholder('https://www.google.com/maps/place/...')
                            ->columnSpanFull(),

                        Textarea::make('address_maps')
                            ->label('Address Maps Embed Code')
                            ->placeholder('<iframe src="..." width="600" height="450"></iframe>')
                            ->rows(4)
                            ->columnSpanFull(),
                    ])
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

        $contactInfo = ContactInformation::first();

        if ($contactInfo) {
            $contactInfo->update($data);
        } else {
            ContactInformation::create($data);
        }

        Notification::make()
            ->title('Contact information saved successfully!')
            ->success()
            ->send();
    }
}