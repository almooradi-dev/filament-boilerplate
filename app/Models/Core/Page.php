<?php

namespace App\Models\Core;

use App\Traits\HasIsActive;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;

class Page extends Model
{
    use SoftDeletes, HasTranslations, HasIsActive;

    protected $guarded = [];

    public $translatable = ['name', 'content'];
}
