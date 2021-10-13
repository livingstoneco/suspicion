<?php

namespace Livingstoneco\Suspicion\Http\Middleware;

use Closure;
use Illuminate\Pipeline\Pipeline;

class IsRequestSuspicious
{
    public function handle($request, Closure $next)
    {
        $pipeline = app(Pipeline::class)
            ->send($request)
            ->through([
                \Livingstoneco\Suspicion\RequestFilters\Keywords::class,
                \Livingstoneco\Suspicion\RequestFilters\Domains::class,
                \Livingstoneco\Suspicion\RequestFilters\TopLevelDomains::class,
                \Livingstoneco\Suspicion\RequestFilters\Cyrillic::class
            ])
            ->thenReturn();

        return $next($request);
    }
}