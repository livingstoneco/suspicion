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
                abort('422', 'We are unable to process your request due to suspicious traffic from your network. If your request is urgent, place contact us by phone.');
            }
        }

        return $next($request);
    }

    // Return array of banned keywords
    private function getBannedKeywords()
    {
        return ['wordpress', 'woocommerce', 'joomla', 'prestashop', 'ecommerce store', 'nymphomania', 'opencart', 'magento', 'bobinternetmarketing', 'business contacts', 'porn', 'capterra', 'dbms', 'whatsapp', 'search engine optimization', 'search results', 'google search', 'guest post', 'social media marketing', 'website traffic', 'no-reply', 'noreply', 'seo', 'smm', 'gay', 'homo', 'homosexual', 'sexual', 'dating', 'romance', 'babes', 'meet singles', 'earn income', 'earn money', 'freetopfast.com', 'xxx', 'email marketing', 'social media marketing', 'marketing solutions', 'blogger', 'article placement', 'service expiration', 'spam', 'medicine', 'human growth hormone', 'life insurance', 'lose weight', 'medicine', 'no medical exams', 'online pharmacy', 'removes wrinkles', 'reverses aging', 'stop snoring', 'valium', 'viagra', 'vicodin', 'weight loss', 'xanax', 'casino', 'paid members', 'lead generation', 'disease', 'romania', 'prostitute'];
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
