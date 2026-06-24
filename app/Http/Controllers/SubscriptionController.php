<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use App\Models\Subscription;
use App\Services\AffiliateService;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    public function __construct(protected AffiliateService $affiliates) {}

    public function index(Request $request)
    {
        return view('subscription.index', [
            'plans' => Plan::where('is_active', true)->orderBy('sort')->get(),
            'current' => $request->user()->activeSubscription,
        ]);
    }

    /**
     * Subscribe to a plan.
     *
     * NOTE: payment capture is gateway-dependent. In 'manual' billing mode the
     * subscription is activated immediately (useful for launch / bank transfer).
     * Wire Redsys or Stripe here to charge before activating — see docs/BILLING.md.
     */
    public function subscribe(Request $request, Plan $plan)
    {
        $user = $request->user();

        // Free plan: just clear any paid subscription.
        if ($plan->isFree()) {
            $user->subscriptions()->where('status', 'active')->update(['status' => 'cancelled', 'cancelled_at' => now()]);

            return redirect()->route('dashboard')->with('status', __('messages.plan_changed'));
        }

        $mode = config('services.billing.mode', 'manual');

        $subscription = Subscription::create([
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'status' => $mode === 'manual' ? 'active' : 'pending',
            'gateway' => $mode,
            'amount_cents' => $plan->price_cents,
            'currency' => $plan->currency,
            'starts_at' => now(),
            'ends_at' => $plan->interval === 'year' ? now()->addYear() : now()->addMonth(),
            'renews_at' => $plan->interval === 'year' ? now()->addYear() : now()->addMonth(),
        ]);

        if ($subscription->status === 'active') {
            // Deactivate previous active subs and reward the referral chain.
            $user->subscriptions()
                ->where('id', '!=', $subscription->id)
                ->where('status', 'active')
                ->update(['status' => 'cancelled', 'cancelled_at' => now()]);

            $this->affiliates->commissionForSubscription($subscription);

            return redirect()->route('dashboard')->with('status', __('messages.subscription_active'));
        }

        // Real gateway flow would redirect to the payment page here.
        return redirect()->route('subscription.index')->with('status', __('messages.payment_pending'));
    }

    public function cancel(Request $request)
    {
        $request->user()->subscriptions()
            ->where('status', 'active')
            ->update(['status' => 'cancelled', 'cancelled_at' => now()]);

        return redirect()->route('subscription.index')->with('status', __('messages.subscription_cancelled'));
    }
}
