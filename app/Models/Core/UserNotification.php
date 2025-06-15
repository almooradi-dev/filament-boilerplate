<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class UserNotification extends Model
{
	use HasFactory, HasTranslations;

	protected $translatable = ['title', 'body'];

	protected $guarded = [];

    protected $casts = [
        'data' => 'array',
    ];
}