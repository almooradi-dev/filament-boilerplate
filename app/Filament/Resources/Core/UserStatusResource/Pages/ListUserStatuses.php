<?php

namespace App\Filament\Resources\Core\UserStatusResource\Pages;

use App\Filament\Resources\Core\UserStatusResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use App\Vendor\Filament\Actions\LocaleSwitcher;
use LaraZeus\SpatieTranslatable\Resources\Pages\ListRecords\Concerns\Translatable;

class ListUserStatuses extends ListRecords
{
    use Translatable;

    protected static string $resource = UserStatusResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            LocaleSwitcher::make(), // TODO: Add to documentation
        ];
    }
}
