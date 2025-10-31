<?php

namespace App\Filament\Resources\Core\PageResource\Pages;

use App\Filament\Resources\Core\PageResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use App\Vendor\Filament\Actions\LocaleSwitcher;
use LaraZeus\SpatieTranslatable\Resources\Pages\EditRecord\Concerns\Translatable;

class EditPage extends EditRecord
{
    use Translatable;

    protected static string $resource = PageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            LocaleSwitcher::make(),
        ];
    }
}
