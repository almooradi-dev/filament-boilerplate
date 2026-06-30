<?php

return [
    // Model (content) translations — spatie/laravel-translatable.
    // See App\Vendor\Filament\Actions\LocaleSwitcher (per-record locale switcher).
    'localization' => [
        'enabled' => true,
        'show_locale_switcher' => false,
    ],

    // Dashboard UI language switcher — bezhansalleh/filament-language-switch.
    // Toggles the panel-wide language switcher independently of model translations.
    'ui_localization' => [
        'enabled' => false,
    ],

    'uploads' => [
        // Default maximum upload size (in KB) applied to FileUpload fields.
        'max_size' => env('FILAMENT_UPLOAD_MAX_SIZE', 2048),
    ],
];
