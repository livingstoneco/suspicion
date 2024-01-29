<?php
namespace Livingstoneco\Suspicion\RequestFilters;

use Closure;
use Illuminate\Support\Facades\DB;
use Livingstoneco\Suspicion\Models\SuspiciousRequest;

class IsRepeatOffender
{
    public function handle($request, Closure $next)
    {
        $offenders = DB::table('suspicious_requests')
            ->select(DB::raw('ip, count(ip)'))
            ->groupBy('ip')
            ->havingRaw('count(ip) >= ?', [config('suspicion.repeat_offenders.threshold')])
            ->get();

        foreach ($offenders as $offender) {
            if ($offender->ip === $request->ip()) {
                $this->logRequest($request);
                abort(config('suspicion.repeat_offenders.http_code'), config('suspicion.repeat_offenders.message'));
            }
        }

        return $next($request);
    }

    // Log suspicious request
    private function logRequest($request)
    {
        $sus = new SuspiciousRequest;
        $sus->ip = $request->ip();
        $sus->method = $request->method();
        $sus->url = $request->url();
        $sus->input = $request->all();
        $sus->headers = $request->header();
        $sus->cookies = $request->cookie();
        $sus->userAgent = $request->useragent();
        $sus->class = get_class($this);
        $sus->trigger = 'Is Repeat Offender';
        $sus->save();
    }
}
