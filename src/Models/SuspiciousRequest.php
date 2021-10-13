<?php

namespace Livingstoneco\Suspicion\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\AsCollection;

class SuspiciousRequest extends Model
{
    protected $guarded = [];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'input' => 'object',
        'headers' => 'object',
        'cookies' => 'object'
    ];
}
