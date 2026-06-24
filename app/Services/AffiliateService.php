<?php

namespace App\Services;

use App\Models\AffiliateClick;
use App\Models\Commission;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Http\Request;

/**
 * Multi-level affiliate program (ganacon style).
 *
 * - A visitor arriving with ?ref=CODE is cookied and the click logged.
 * - On registration the new user is attached to the referrer (referred_by).
 * - When that user pays for a subscription, commissions are generated for up to
 *   three levels of the referral chain using the rates in config/translator.php.
 */
class AffiliateService
{
    public function cookieName(): string
    {
        return 'ref';
    }

    /** Record a referral visit and return the affiliate, if the code is valid. */
    public function trackClick(string $code, Request $request): ?User
    {
        $affiliate = User::where('affiliate_code', $code)->first();

        if (! $affiliate) {
            return null;
        }

        AffiliateClick::create([
            'affiliate_code' => $code,
            'affiliate_id' => $affiliate->id,
            'ip_address' => $request->ip(),
            'user_agent' => substr((string) $request->userAgent(), 0, 255),
            'referer' => substr((string) $request->headers->get('referer'), 0, 255),
            'landing' => substr($request->fullUrl(), 0, 255),
        ]);

        return $affiliate;
    }

    /** Resolve the referring user from the request cookie. */
    public function referrerFromRequest(Request $request): ?User
    {
        $code = $request->cookie($this->cookieName());

        return $code ? User::where('affiliate_code', $code)->first() : null;
    }

    /**
     * Generate multi-level commissions for a paid subscription.
     * Idempotent per subscription.
     */
    public function commissionForSubscription(Subscription $subscription): void
    {
        if ($subscription->amount_cents <= 0) {
            return;
        }

        if (Commission::where('subscription_id', $subscription->id)->exists()) {
            return; // already processed
        }

        $rates = config('translator.affiliate.levels', []);
        $payer = $subscription->user;
        $current = $payer?->referrer;
        $level = 1;

        while ($current && $level <= 3) {
            $rate = (float) ($rates[$level] ?? 0);

            if ($rate > 0) {
                Commission::create([
                    'affiliate_id' => $current->id,
                    'source_user_id' => $payer->id,
                    'subscription_id' => $subscription->id,
                    'level' => $level,
                    'rate' => $rate,
                    'amount_cents' => (int) round($subscription->amount_cents * $rate / 100),
                    'currency' => $subscription->currency,
                    'status' => 'pending',
                ]);
            }

            $current = $current->referrer;
            $level++;
        }

        AffiliateClick::where('affiliate_id', $payer->referred_by)
            ->where('converted', false)
            ->latest()
            ->limit(1)
            ->update(['converted' => true]);
    }
}
