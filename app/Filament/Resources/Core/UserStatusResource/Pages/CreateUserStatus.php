<?php

namespace App\Filament\Resources\Core\UserStatusResource\Pages;

use App\Filament\Resources\Core\UserStatusResource;
use App\Vendor\Filament\Actions\LocaleSwitcher;
use Filament\Resources\Pages\CreateRecord;

class CreateUserStatus extends CreateRecord
{
    use CreateRecord\Concerns\Translatable;

    protected static string $resource = UserStatusResource::class;

    protected function getHeaderActions(): array
    {
        return [
            LocaleSwitcher::make(),
        ];
    }
}
