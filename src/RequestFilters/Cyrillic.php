<?php

namespace Livingstoneco\Suspicion\RequestFilters;

use Closure;
use Illuminate\Support\Str;
use Livingstoneco\Suspicion\Models\SuspiciousRequest;

class Cyrillic
{

	public function handle($request, Closure $next)
	{
		// Loop through request parameters to determine if they contain references to a banned domain
		foreach($request->all() as $input) {
            if(preg_match('/[\p{Cyrillic}]/u', $input))
            {
            	$this->logRequest($request);
            	abort('422','We are unable to process your request due to suspicious traffic from your network. If your request is urgent, place contact our store by phone.');
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