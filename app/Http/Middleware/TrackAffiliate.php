<?php

namespace App\Http\Middleware;

use App\Services\AffiliateService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Symfony\Component\HttpFoundation\Response;

class TrackAffiliate
{
    public function __construct(protected AffiliateService $affiliates) {}

    public function handle(Request $request, Closure $next): Response
    {
        $code = $request->query('ref');

        if ($code && ! $request->cookie($this->affiliates->cookieName())) {
            if ($this->affiliates->trackClick($code, $request)) {
                $days = (int) config('translator.affiliate.cookie_days', 30);
                Cookie::queue($this->affiliates->cookieName(), $code, $days * 24 * 60);
            }
        }

        return $next($request);
    }
}
