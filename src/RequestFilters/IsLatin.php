<?php

namespace Livingstoneco\Suspicion\RequestFilters;

use Closure;
use Illuminate\Support\Str;
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
        foreach ($request->except(['_token', 'g-recaptcha-response']) as $input) {
            $matchedRegex = $this->containsNonLatin($input);
            if ($matchedRegex !== null) {
                $regex = Str::between($matchedRegex, '{', '}');
                $this->logRequest($request, $regex);
                abort('422', config('suspicion.error_message'));
            }
        }

        return $next($request);
    }

    /**
     * Recursively check if input contains non-Latin characters
     *
     * @param mixed $input
     * @return string|null Returns the matched regex pattern or null
     */
    private function containsNonLatin($input)
    {
        if (is_array($input)) {
            foreach ($input as $value) {
                $result = $this->containsNonLatin($value);
                if ($result !== null) {
                    return $result;
                }
            }
            return null;
        }

        if (!is_string($input)) {
            return null;
        }

        foreach ($this->langRegex as $regex) {
            if (preg_match($regex, $input)) {
                return $regex;
            }
        }

        return null;
    }

    // Log suspicious request
    private function logRequest($request, $regex)
    {
        $sus = new SuspiciousRequest;
        $sus->ip = $request->ip();
        $sus->method = $request->method();
        $sus->url = $request->url();
        $sus->input = $request->all();
        $sus->headers = $request->header();
        $sus->cookies = $request->cookie();
        $sus->userAgent = mb_convert_encoding($request->useragent(), 'UTF-8', 'UTF-8');
        $sus->class = get_class($this);
        $sus->trigger = 'Conatins ' . $regex;
        $sus->save();
    }
}
