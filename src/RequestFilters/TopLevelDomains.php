<?php
namespace Livingstoneco\Suspicion\RequestFilters;

use Closure;
use Illuminate\Support\Str;
use Livingstoneco\Suspicion\Models\SuspiciousRequest;

class TopLevelDomains
{
    private $topLevelDomains;

    public function __construct()
    {
        $this->topLevelDomains = $this->getBannedTopLevelDomains();
    }

    public function handle($request, Closure $next)
    {
        // Loop through request parameters to determine if they contain references to a banned top level domain
        foreach ($request->all() as $input) {
            $value = strtolower($input);

            if (Str::endsWith($value, $this->topLevelDomains)) {
                $this->logRequest($request);
                abort('422', config('suspicion.error_message'));
            }
        }

        return $next($request);
    }

    // Return array of banned top level domains
    private function getBannedTopLevelDomains()
    {
        return ['.test', '.tst', '.ru'];
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
