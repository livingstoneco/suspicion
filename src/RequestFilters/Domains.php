<?php

namespace Livingstoneco\Suspicion\RequestFilters;

use Closure;
use Illuminate\Support\Str;
use Livingstoneco\Suspicion\Models\SuspiciousRequest;

class Domains
{
    private $domains;

    public function __construct()
    {
        $this->domains = $this->getBannedDomains();
    }

    public function handle($request, Closure $next)
    {
        // Loop through request parameters to determine if they contain references to a banned domain
        foreach ($request->all() as $input) {
            $value = strtolower($input);

            if (Str::endsWith($value, $this->domains)) {
                $this->logRequest($request);
                abort('422', config('suspicion.error_message'));
            }
        }

        return $next($request);
    }

    // Return array of banned domains
    private function getBannedDomains()
    {
        return ['test.com', 'test.ca', 'test.net', 'qgp.com', 'test.org', 'mail.ru', 'yandex.com', 'qualityguestposts.com', 'rambler.ru', 'qualitybloggeroutreach.com', 'coldreach.rocks', 'grocket.net', 'mailbanger.com', 'starmedia.ca', 'foxandfigcafe.com', 'migfoam.com', ' buktrk.com', 'freetopfast.com', 'nikitosgross.pw', 'mixfilesmaker.com', 'stancopak.net', 'lone1y.com', 'topworldnewstoday.com', 'jasper-robot.com', 'validbelt.com', 'xtreflectivefilm.com', 'hfxtreflectivefilm.com', 'smartaiwriting.com', 'fiverrseoer.com', 'askgloves.com', 'reputationresults.net', 'fiverr.com', 'bit.ly', 'bitly.com', 'fromfuture.io'];
    }

    // Log suspicious request
    private function logRequest($request)
    {
        $sus = new SuspiciousRequest();
        $sus->ip = $request->ip();
        $sus->method = $request->method();
        $sus->url = $request->url();
        $sus->input = $request->all();
        $sus->headers = $request->header();
        $sus->cookies = $request->cookie();
        $sus->userAgent = $request->useragent();
        $sus->trigger = get_class($this);
        $sus->save();
    }
}
