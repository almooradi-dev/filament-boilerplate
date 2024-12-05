<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('core.app_name', 'App Name');
        $this->migrator->add('core.logo_light', null);
        $this->migrator->add('core.logo_dark', null);
        $this->migrator->add('core.default_avatar', null);
    }
};
