<?php

namespace App\Filament\Clusters;

use BackedEnum;
use Filament\Clusters\Cluster;

class Status extends Cluster
{
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-signal';

    public static function getNavigationGroup(): ?string
    {
        return __('core.administration');
    }
}
