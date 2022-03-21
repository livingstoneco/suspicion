<?php
namespace Livingstoneco\Suspicion\RequestFilters;

use Closure;
use Livingstoneco\Suspicion\Models\SuspiciousRequest;

class IsLatin
{
    private $langRegex;

    public function __construct()
    {
        $this->langRegex = [
            '/[\p{Arabic}]/u',
            '/[\p{Armenian}]/u',
            '/[\p{Bengali}]/u',
            '/[\p{Bopomofo}]/u',
            '/[\p{Braille}]/u',
            '/[\p{Buhid}]/u',
            '/[\p{Canadian_Aboriginal}]/u',
            '/[\p{Cherokee}]/u',
            '/[\p{Cyrillic}]/u',
            '/[\p{Devanagari}]/u',
            '/[\p{Ethiopic}]/u',
            '/[\p{Georgian}]/u',
            '/[\p{Greek}]/u',
            '/[\p{Gujarati}]/u',
            '/[\p{Gurmukhi}]/u',
            '/[\p{Han}]/u',
            '/[\p{Hangul}]/u',
            '/[\p{Hanunoo}]/u',
            '/[\p{Hebrew}]/u',
            '/[\p{Hiragana}]/u',
            '/[\p{Inherited}]/u',
            '/[\p{Kannada}]/u',
            '/[\p{Katakana}]/u',
            '/[\p{Khmer}]/u',
            '/[\p{Lao}]/u',
            '/[\p{Limbu}]/u',
            '/[\p{Malayalam}]/u',
            '/[\p{Mongolian}]/u',
            '/[\p{Myanmar}]/u',
            '/[\p{Ogham}]/u',
            '/[\p{Oriya}]/u',
            '/[\p{Runic}]/u',
            '/[\p{Sinhala}]/u',
            '/[\p{Syriac}]/u',
            '/[\p{Tagalog}]/u',
            '/[\p{Tagbanwa}]/u',
            '/[\p{Tamil}]/u',
            '/[\p{Telugu}]/u',
            '/[\p{Thaana}]/u',
            '/[\p{Thai}]/u',
            '/[\p{Tibetan}]/u',
            '/[\p{Yi}]/u',
        ];
    }

    public function handle($request, Closure $next)
    {
        // Loop through request parameters to determine if any parameters contains a foreign language
        foreach ($request->all() as $input) {
            foreach ($this->langRegex as $regex) {
                if (preg_match($regex, $input)) {
                    $this->logRequest($request);
                    abort('422', config('suspicion.error_message'));
                }
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
        $sus->trigger = get_class($this);
        $sus->save();
    }
}
