<?php

namespace Livingstoneco\Suspicion\Models;

use Illuminate\Database\Eloquent\Model;
use Livingstoneco\Suspicion\Casts\SafeJson;

class SuspiciousRequest extends Model
{
    protected $guarded = [];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'input' => SafeJson::class,
        'headers' => SafeJson::class,
        'cookies' => SafeJson::class
    ];
}
