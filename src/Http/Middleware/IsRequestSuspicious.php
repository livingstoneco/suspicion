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
                \Livingstoneco\Suspicion\RequestFilters\IsRepeatOffender::class,
                \Livingstoneco\Suspicion\RequestFilters\IsLatin::class,
                \Livingstoneco\Suspicion\RequestFilters\TopLevelDomains::class,
                \Livingstoneco\Suspicion\RequestFilters\Domains::class,
                \Livingstoneco\Suspicion\RequestFilters\Keywords::class,
            ])
            ->thenReturn();

        return $next($request);
    }
}
