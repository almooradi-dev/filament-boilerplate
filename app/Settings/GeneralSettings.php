<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class CoreSettings extends Settings
{
    public string $app_name;
    public ?string $logo_light;
    public ?string $logo_dark;
    public ?string $default_avatar;

    public static function group(): string
    {
        return 'core';
    }
}
