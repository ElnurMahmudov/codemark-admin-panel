<?php

namespace App\Filament\Resources\CustomerLogos\Pages;

use App\Filament\Resources\CustomerLogos\CustomerLogoResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditCustomerLogo extends EditRecord
{
    protected static string $resource = CustomerLogoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
