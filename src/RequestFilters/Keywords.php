<?php
namespace Livingstoneco\Suspicion\RequestFilters;

use Closure;
use Illuminate\Support\Str;
use Livingstoneco\Suspicion\Models\SuspiciousRequest;

class Keywords
{
    private $keywords;

    public function __construct()
    {
        $this->keywords = $this->getBannedKeywords();
    }

    public function handle($request, Closure $next)
    {
        // Loop through request parameters to determine if they contain banned keywords
        foreach ($request->all() as $input) {
            $value = strtolower($input);

            if (Str::contains($value, $this->keywords)) {
                $this->logRequest($request);
                abort('422', config('suspicion.error_message'));
            }
        }

        return $next($request);
    }

    // Return array of banned keywords
    private function getBannedKeywords()
    {
        return ['wordpress', 'woocommerce', 'joomla', 'spa', 'affiliate program', 'prestashop', 'financial', 'B2B', 'ecommerce store', 'webmasters', 'nymphomania', 'opencart', 'forex', 'magento', 'bobinternetmarketing', 'business contacts', 'porn', 'capterra', 'dbms', 'whatsapp', 'search engine optimization', 'search results', 'google search', 'guest post', 'social media marketing', 'website traffic', 'no-reply', 'noreply', 'seo', 'smm', 'gay', 'homo', 'homosexual', 'sexual', 'dating', 'romance', 'babes', 'meet singles', 'earn income', 'earn money', 'freetopfast.com', 'xxx', 'marketing', 'blogger', 'article placement', 'service expiration', 'spam', 'medicine', 'human growth hormone', 'life insurance', 'lose weight', 'medicine', 'no medical exams', 'online pharmacy', 'removes wrinkles', 'reverses aging', 'stop snoring', 'valium', 'viagra', 'vicodin', 'weight loss', 'xanax', 'casino', 'paid members', 'lead generation', 'disease', 'romania', 'prostitute', 'mailing list'];
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
