<?php

namespace App\Filament\Resources\Core\UserStatusResource\Pages;

use App\Filament\Resources\Core\UserStatusResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use App\Vendor\Filament\Actions\LocaleSwitcher;

class EditUserStatus extends EditRecord
{
    use EditRecord\Concerns\Translatable;

    protected static string $resource = UserStatusResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            Actions\ForceDeleteAction::make(),
            Actions\RestoreAction::make(),
            LocaleSwitcher::make(),
        ];
    }
}
