<?php

namespace Livingstoneco\Suspicion\RequestFilters;

use Closure;
use Livingstoneco\Suspicion\Models\SuspiciousRequest;

class Keywords
{
    private $keywords;

    public function __construct()
    {
        $this->keywords = $this->getKeywords();
    }

    public function handle($request, Closure $next)
    {
        // Loop through request parameters to determine if they contain banned keywords
        foreach ($request->except(['_token', 'g-recaptcha-response']) as $input) {
            foreach ($this->keywords as $keyword) {
                if (preg_match('/\b' . preg_quote($keyword) . '\b/mi', $input)) {
                    $this->logRequest($request, $keyword);
                    abort('422', config('suspicion.error_message'));
                }
            }
        }

        return $next($request);
    }

    // Return array of banned keywords
    private function getKeywords()
    {
        return [
            'wordpress',
            'woocommerce',
            'joomla',
            'spa',
            'affiliate program',
            'prestashop',
            'homeopathic',
            'incentives',
            'deflationary',
            'recompound',
            'crypto',
            'ROI',
            'binance',
            'reinvestment',
            'investment',
            'token',
            'financial',
            'B2B',
            'ecommerce store',
            'webmasters',
            'nymphomania',
            'opencart',
            'forex',
            'magento',
            'bobinternetmarketing',
            'business contacts',
            'pornography',
            'porn',
            'capterra',
            'dbms',
            'whatsapp',
            'search engine optimization',
            'search results',
            'google search',
            'guest post',
            'social media',
            'website traffic',
            'no-reply',
            'noreply',
            'seo',
            'smm',
            'gay',
            'homo',
            'homosexual',
            'sexual',
            'sex',
            'bombay',
            'mumbai',
            'india',
            'dating',
            'romance',
            'babes',
            'meet singles',
            'earn income',
            'earn money',
            'xxx',
            'marketing',
            'blogger',
            'article placement',
            'service expiration',
            'spam',
            'medicine',
            'human growth hormone',
            'life insurance',
            'lose weight',
            'medicine',
            'no medical exams',
            'online pharmacy',
            'removes wrinkles',
            'reverses aging',
            'stop snoring',
            'valium',
            'viagra',
            'vicodin',
            'weight loss',
            'xanax',
            'casino',
            'paid members',
            'lead generation',
            'disease',
            'romania',
            'prostitute',
            'mailing list',
            'php',
            'asp',
            'cdb',
            'thc',
            'delta 8',
            'cialis',
            'hair loss',
            'data recovery',
            'nudes',
            'hardcore',
            'abc',
            'url=',
            'captcha',
            'product key',
            'homeopathic',
            'nausea',
            'remedy',
            'remedies',
            'online chat',
            'chat online',
            'sweatheart',
            'pakistan',
            'href',
            'cock',
            'suck',
            'partnerships',
            'lolita',
            'fuck',
            'loli',
            'foto',
            'cp',
            'pthc',
            'clinical',
            'cbd',
            'cbn',
            'pussy',
            'masterbate',
            'masterbating',
            'tits',
            'adsense',
            'tutorial',
            'platform',
            'payouts',
            'algorithms',
            'machine learning',
            'machine-learning',
            'artificial intelligence',
            'ai',
            'revenue',
            'robot',
            'automate',
            'backlinks',
            'optimization',
            'reputation',
            'freelancer',
            'public relations',
            'advertising',
            'conversion',
            'conversions',
            'traffic',
            'optimized',
            'optimize',
            'domain',
            'payable',
            'wikipedia',
            'telegram',
            'bitcoin',
            'cryptocurrency',
            'skyrocketing',
            'instagram',
            'popularity',
            'social growth',
            'workshop',
            'sem',
            'fortune',
            'marketplace',
        ];
    }

    // Log suspicious request
    private function logRequest($request, $keyword)
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
        $sus->trigger = $keyword;
        $sus->save();
    }
}
