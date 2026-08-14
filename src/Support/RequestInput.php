<?php

namespace Livingstoneco\Suspicion\Support;

use Illuminate\Http\Request;

class RequestInput
{
    public static function fieldsToInspect(Request $request): array
    {
        return $request->except(config('suspicion.skip_fields', ['_token', 'g-recaptcha-response']));
    }
}
