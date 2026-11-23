<?php

namespace App\Filament\Resources\CustomerLogos;

use App\Filament\Resources\CustomerLogos\Pages\CreateCustomerLogo;
use App\Filament\Resources\CustomerLogos\Pages\EditCustomerLogo;
use App\Filament\Resources\CustomerLogos\Pages\ListCustomerLogos;
use App\Filament\Resources\CustomerLogos\Schemas\CustomerLogoForm;
use App\Filament\Resources\CustomerLogos\Tables\CustomerLogosTable;
use App\Models\CustomerLogo;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class CustomerLogoResource extends Resource
{
    protected static ?string $model = CustomerLogo::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-photo';

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?int $navigationSort = 1;

    public static function getNavigationGroup(): string|\UnitEnum|null
    {
        return 'Məzmun İdarəetməsi';
    }

    public static function getNavigationLabel(): string
    {
        return 'Müştəri Loqoları';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Müştəri Loqoları';
    }

    public static function getModelLabel(): string
    {
        return 'Müştəri Loqosu';
    }

    public static function form(Schema $schema): Schema
    {
        return CustomerLogoForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CustomerLogosTable::configure($table);
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
            'index' => ListCustomerLogos::route('/'),
            'create' => CreateCustomerLogo::route('/create'),
            'edit' => EditCustomerLogo::route('/{record}/edit'),
        ];
    }
}
