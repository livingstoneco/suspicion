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
        foreach ($request->except(['_token', 'g-recaptcha-response']) as $input) {
            $matchedDomain = $this->containsDomain($input);
            if ($matchedDomain !== null) {
                $this->logRequest($request, $matchedDomain);
                abort(403, config('suspicion.error_message'));
            }
        }

        return $next($request);
    }

    /**
     * Recursively check if input contains banned domains
     *
     * @param mixed $input
     * @return string|null Returns the matched domain or null
     */
    private function containsDomain($input)
    {
        if (is_array($input)) {
            foreach ($input as $value) {
                $result = $this->containsDomain($value);
                if ($result !== null) {
                    return $result;
                }
            }
            return null;
        }

        if (!is_string($input)) {
            return null;
        }

        foreach ($this->domains as $domain) {
            if (preg_match("/" . preg_quote($domain) . '/mi', $input)) {
                return $domain;
            }
        }

        return null;
    }

    // Return array of banned domains
    private function getBannedDomains()
    {
        return ['test.com', 'test.ca', 'test.net', 'qgp.com', 'test.org', 'mail.ru', 'yandex.com', 'qualityguestposts.com', 'rambler.ru', 'qualitybloggeroutreach.com', 'coldreach.rocks', 'grocket.net', 'mailbanger.com', 'starmedia.ca', 'foxandfigcafe.com', 'migfoam.com', ' buktrk.com', 'freetopfast.com', 'nikitosgross.pw', 'mixfilesmaker.com', 'stancopak.net', 'lone1y.com', 'topworldnewstoday.com', 'jasper-robot.com', 'validbelt.com', 'xtreflectivefilm.com', 'hfxtreflectivefilm.com', 'smartaiwriting.com', 'fiverrseoer.com', 'askgloves.com', 'reputationresults.net', 'fiverr.com', 'bit.ly', 'bitly.com', 'fromfuture.io', ' businesstical.com', ' premiumdomainslist.com', 'wiki-moderator.com', 'speed-seo.net', 't.me', 't-online.com', 'medicopostura.com', 'socialbusybee.com', 'tinyurl.com', 'form.com', 'blogspot.com', 'short.gy', 'customreviewscard.com'];
    }

    // Log suspicious request
    private function logRequest($request, $domain)
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
        $sus->trigger = $domain;
        $sus->save();
    }
}
