<?php

namespace App\Filament\Resources\Core\UserStatusResource\Pages;

use App\Filament\Resources\Core\UserStatusResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use App\Vendor\Filament\Actions\LocaleSwitcher;

class ListUserStatuses extends ListRecords
{
    use ListRecords\Concerns\Translatable;

    protected static string $resource = UserStatusResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            LocaleSwitcher::make(), // TODO: Add to documentation
        ];
    }
}
