<?php

namespace App\Models\Core;

use App\Traits\HasIsActive;
use App\Traits\HasTranslations;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Page extends Model
{
    use SoftDeletes, HasTranslations, HasIsActive;

    protected $guarded = [];

    public $translatable = ['name', 'content', 'metadata'];
}
