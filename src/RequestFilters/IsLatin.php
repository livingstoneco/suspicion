<?php
namespace Livingstoneco\Suspicion\RequestFilters;

use Closure;
use Livingstoneco\Suspicion\Models\SuspiciousRequest;

class IsLatin
{
    public function handle($request, Closure $next)
    {
        // Loop through request parameters to determine if they contain references to a banned domain
        foreach ($request->all() as $input) {
            if (!preg_match('/[\p{Latin}]/u', $input)) {
                $this->logRequest($request);
                abort('422', config('suspicion.error_message'));
            }
        }

        return $next($request);
    }

    // Log suspicious request
    private function logRequest($request)
    {
        $sus = new SuspiciousRequest;
        $sus->ip = $request->ip();
        $sus->url = $request->url();
        $sus->input = $request->all();
        $sus->headers = $request->header();
        $sus->cookies = $request->cookie();
        $sus->userAgent = $request->useragent();
        $sus->save();
    }
}
