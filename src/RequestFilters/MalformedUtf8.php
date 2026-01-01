<?php

namespace Livingstoneco\Suspicion\RequestFilters;

use Closure;
use Livingstoneco\Suspicion\Models\SuspiciousRequest;

class MalformedUtf8
{
    public function handle($request, Closure $next)
    {
        // Check request parameters for malformed UTF-8
        foreach ($request->except(['_token', 'g-recaptcha-response']) as $input) {
            if ($this->containsMalformedUtf8($input)) {
                $this->logRequest($request);
                abort(403, config('suspicion.error_message'));
            }
        }

        // Check headers for malformed UTF-8
        foreach ($request->header() as $header) {
            if ($this->containsMalformedUtf8($header)) {
                $this->logRequest($request);
                abort(403, config('suspicion.error_message'));
            }
        }

        // Check cookies for malformed UTF-8
        foreach ($request->cookie() as $cookie) {
            if ($this->containsMalformedUtf8($cookie)) {
                $this->logRequest($request);
                abort(403, config('suspicion.error_message'));
            }
        }

        return $next($request);
    }

    /**
     * Recursively check if input contains malformed UTF-8
     *
     * @param mixed $input
     * @return bool
     */
    private function containsMalformedUtf8($input)
    {
        if (is_array($input)) {
            foreach ($input as $value) {
                if ($this->containsMalformedUtf8($value)) {
                    return true;
                }
            }
            return false;
        }

        if (!is_string($input)) {
            return false;
        }

        // Check if the string contains invalid UTF-8 sequences
        return !mb_check_encoding($input, 'UTF-8');
    }

    // Log suspicious request
    private function logRequest($request)
    {
        $sus = new SuspiciousRequest();
        $sus->ip = $request->ip();
        $sus->method = $request->method();
        $sus->url = $request->url();
        $sus->input = $this->sanitizeUtf8($request->all());
        $sus->headers = $this->sanitizeUtf8($request->header());
        $sus->cookies = $this->sanitizeUtf8($request->cookie());
        $sus->userAgent = $this->sanitizeUtf8($request->useragent());
        $sus->class = get_class($this);
        $sus->trigger = 'Malformed UTF-8 detected';
        $sus->save();
    }

    /**
     * Recursively sanitize data by removing invalid UTF-8 sequences
     *
     * @param mixed $data
     * @return mixed
     */
    private function sanitizeUtf8($data)
    {
        if (is_array($data)) {
            $sanitized = [];
            foreach ($data as $key => $value) {
                // Sanitize both keys and values
                $sanitizedKey = is_string($key) ? $this->sanitizeString($key) : $key;
                $sanitized[$sanitizedKey] = $this->sanitizeUtf8($value);
            }
            return $sanitized;
        }

        if (!is_string($data)) {
            return $data;
        }

        return $this->sanitizeString($data);
    }

    /**
     * Sanitize a single string by removing invalid UTF-8 sequences
     *
     * @param string $data
     * @return string
     */
    private function sanitizeString($data)
    {
        // Remove invalid UTF-8 sequences using iconv
        // The //IGNORE flag will remove invalid sequences
        $sanitized = @iconv('UTF-8', 'UTF-8//IGNORE', $data);

        // Fallback to mb_convert_encoding if iconv fails
        if ($sanitized === false) {
            $sanitized = mb_convert_encoding($data, 'UTF-8', 'UTF-8');
        }

        return $sanitized;
    }
}
