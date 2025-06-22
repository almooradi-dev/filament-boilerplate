<?php

namespace App\Models\Core;

use App\Traits\HasTranslations;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserNotification extends Model
{
	use HasFactory, HasTranslations;

	protected $translatable = ['title', 'body'];

	protected $guarded = [];

    protected $casts = [
        'data' => 'array',
    ];
}