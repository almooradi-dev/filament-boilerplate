<?php

namespace App\Providers\Filament;

use Filament\Forms\Components\FileUpload;
use Filament\Infolists\Components\ImageEntry;
use Filament\Tables\Columns\ImageColumn;
use Illuminate\Support\ServiceProvider;
use Filament\Tables\Table;

class FilamentCustomizationServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        FileUpload::configureUsing(function (FileUpload $component) {
            $component->visibility('public');
            $component->disk('public');
        });

        ImageColumn::configureUsing(function (ImageColumn $component) {
            $component->visibility('public');
            $component->disk('public');
        });

        ImageEntry::configureUsing(function (ImageEntry $component) {
            $component->visibility('public');
            $component->disk('public');
        });

        Table::configureUsing(function (Table $table) {
            $table->paginationPageOptions([5, 10, 25, 50, 100]); // Removed the "all" option to prevent performance issues on pages with large datasets
            $table->emptyStateHeading('');
            $table->emptyStateDescription(__('core.no_data_was_found'));
        });
    }
}
