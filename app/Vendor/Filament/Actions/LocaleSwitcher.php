<?php

namespace App\Vendor\Filament\Actions;

use Closure;
use LaraZeus\SpatieTranslatable\Actions\LocaleSwitcher as ActionsLocaleSwitcher;

class LocaleSwitcher extends ActionsLocaleSwitcher
{
    public function isVisible(): bool
    {
        return config('filament.localization.enabled', false) && config('filament.localization.show_locale_switcher', true);
    }
}
