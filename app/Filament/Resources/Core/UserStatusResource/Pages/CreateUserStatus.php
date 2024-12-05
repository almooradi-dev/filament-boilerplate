<?php

namespace App\Filament\Resources\Core\UserStatusResource\Pages;

use App\Filament\Resources\Core\UserStatusResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateUserStatus extends CreateRecord
{
    use CreateRecord\Concerns\Translatable;

    protected static string $resource = UserStatusResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\LocaleSwitcher::make(),
        ];
    }
}
