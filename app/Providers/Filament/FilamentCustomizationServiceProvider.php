<?php

namespace App\Providers\Filament;

use Illuminate\Support\ServiceProvider;
use Filament\Tables\Table;

class FilamentCustomizationServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Table::macro('withGlobalCustomization', function () {
            /** @var Table $table */
            $table = $this;

            return $table
                ->emptyStateHeading('')
                ->emptyStateDescription(__('core.no_data_was_found'));
        });
    }
}

