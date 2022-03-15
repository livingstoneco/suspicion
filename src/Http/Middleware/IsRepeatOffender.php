<?php
namespace Livingstoneco\Suspicion\Http\Middleware;

use Closure;
use Illuminate\Pipeline\Pipeline;

class IsRepeatOffender
{
    public function handle($request, Closure $next)
    {
        $pipeline = app(Pipeline::class)
            ->send($request)
            ->through([
                \Livingstoneco\Suspicion\RequestFilters\IsRepeatOffender::class,
            ])
            ->thenReturn();

        return $next($request);
    }
}
