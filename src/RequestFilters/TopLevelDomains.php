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
        foreach ($request->except(['_token', 'g-recaptcha-response']) as $input) {
            foreach ($this->topLevelDomains as $tld) {
                if (preg_match("/" . preg_quote($tld) . '/mi', $input)) {
                    $this->logRequest($request, $tld);
                    abort('422', config('suspicion.error_message'));
                }
            }
        }

        return $next($request);
    }

    // Return array of banned top level domains
    private function getBannedTopLevelDomains()
    {
        return ['.test', '.tst', '.ru', 'xyz', '.online', '.ml', '.tk', '.cf', '.gl', '.pw', '.fi', '.nl', '.az', '.us', '.shop', '.pro', '.site', '.online', '.fun', '.space', '.link', '.top'];
    }

    // Log suspicious request
    private function logRequest($request, $tld)
    {
        $sus = new SuspiciousRequest();
        $sus->ip = $request->ip();
        $sus->method = $request->method();
        $sus->url = $request->url();
        $sus->input = $request->all();
        $sus->headers = $request->header();
        $sus->cookies = $request->cookie();
        $sus->userAgent = mb_convert_encoding($request->useragent(), 'UTF-8', 'UTF-8');
        $sus->class = get_class($this);
        $sus->trigger = $tld;
        $sus->save();
    }
}
