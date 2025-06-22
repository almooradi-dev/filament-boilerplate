<?php

namespace App\Models\Core;

use App\Traits\HasTranslations;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserStatus extends Model
{
    use SoftDeletes, HasTranslations;

    protected $guarded = [];

    public $translatable = ['name'];

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
}
