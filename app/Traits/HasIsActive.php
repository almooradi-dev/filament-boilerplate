<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;

trait HasIsActive
{
    /**
     * Scope active statuses
     *
     * @param Builder $query
     * @return void
     */
    public function scopeWhereActive(Builder $query)
    {
        $query->where('is_active', 1);
    }

    /**
     * Scope active statuses
     *
     * @param Builder $query
     * @return void
     */
    public function scopeWhereInActive(Builder $query)
    {
        $query->where('is_active', 0);
    }
}
