<?php

namespace App\Filament\Pages;

use App\Settings\CoreSettings;
use BackedEnum;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Pages\SettingsPage;
use Filament\Resources\Pages\EditRecord\Concerns\Translatable;
use Filament\Schemas\Schema;
use Illuminate\Contracts\Support\Htmlable;

class CoreSettingsPage extends SettingsPage
{
    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static string $settings = CoreSettings::class;

    public static function getNavigationGroup(): ?string
    {
        return __('core.administration');
    }

    public function getTitle(): string | Htmlable
    {
        return __('core.settings');
    }

    public static function getNavigationLabel(): string
    {
        return __('core.settings');
    }

    public function schema(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('app_name')
                    ->required(),
                FileUpload::make('logo_light')
                    ->label(__('core.logo_light'))
                    ->moveFiles()
                    ->image()
                    ->directory('settings/core')
                    ->openable()
                    ->downloadable(),
                FileUpload::make('logo_dark')
                    ->label(__('core.logo_dark'))
                    ->moveFiles()
                    ->image()
                    ->directory('settings/core')
                    ->openable()
                    ->downloadable(),
                FileUpload::make('default_avatar')
                    ->label(__('core.default_avatar'))
                    ->moveFiles()
                    ->image()
                    ->directory('settings/core')
                    ->openable()
                    ->downloadable()
            ]);
    }
}
