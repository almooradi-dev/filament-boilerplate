<?php

namespace App\Filament\Resources\Core\PageResource\Pages;

use App\Filament\Resources\Core\PageResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use App\Vendor\Filament\Actions\LocaleSwitcher;
use LaraZeus\SpatieTranslatable\Resources\Pages\ListRecords\Concerns\Translatable;

class ListPages extends ListRecords
{
    use Translatable;

    protected static string $resource = PageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            LocaleSwitcher::make(),
        ];
    }
}
