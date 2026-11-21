<?php

namespace App\Filament\Resources\CustomerLogos\Pages;

use App\Filament\Resources\CustomerLogos\CustomerLogoResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListCustomerLogos extends ListRecords
{
    protected static string $resource = CustomerLogoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
