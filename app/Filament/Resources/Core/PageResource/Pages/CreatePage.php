<?php

namespace App\Filament\Resources\Core\PageResource\Pages;

use App\Filament\Resources\Core\PageResource;
use Filament\Resources\Pages\CreateRecord;
use App\Vendor\Filament\Actions\LocaleSwitcher;
use LaraZeus\SpatieTranslatable\Resources\Pages\CreateRecord\Concerns\Translatable;

class CreatePage extends CreateRecord
{
    use Translatable;

    protected static string $resource = PageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            LocaleSwitcher::make(),
        ];
    }
}
