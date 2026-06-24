<?php

namespace App\Http\Controllers;

use App\Models\Payout;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class AffiliateController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        return view('affiliate.index', [
            'link' => url('/').'?ref='.$user->affiliate_code,
            'code' => $user->affiliate_code,
            'rates' => config('translator.affiliate.levels'),
            'minPayout' => config('translator.affiliate.min_payout_cents'),
            'referrals' => $user->referrals()->withCount('referrals')->latest()->take(50)->get(),
            'referralCount' => $user->referrals()->count(),
            'commissions' => $user->commissions()->with('sourceUser')->latest()->take(50)->get(),
            'pendingCents' => (int) $user->commissions()->where('status', 'pending')->sum('amount_cents'),
            'approvedCents' => $user->availableBalanceCents(),
            'paidCents' => (int) $user->commissions()->where('status', 'paid')->sum('amount_cents'),
            'payouts' => $user->payouts()->latest()->take(20)->get(),
        ]);
    }

    public function requestPayout(Request $request)
    {
        $data = $request->validate([
            'method' => ['required', 'in:paypal,bank'],
            'destination' => ['required', 'string', 'max:255'],
        ]);

        $user = $request->user();
        $available = $user->availableBalanceCents();
        $min = (int) config('translator.affiliate.min_payout_cents', 2000);

        if ($available < $min) {
            throw ValidationException::withMessages([
                'amount' => __('messages.payout_min', ['amount' => number_format($min / 100, 2)]),
            ]);
        }

        $payout = Payout::create([
            'user_id' => $user->id,
            'amount_cents' => $available,
            'currency' => 'EUR',
            'method' => $data['method'],
            'destination' => $data['destination'],
            'status' => 'requested',
        ]);

        // Tie the approved commissions to this payout so they stop counting as available.
        // The admin marks the payout as paid, which flips these commissions to 'paid'.
        $user->commissions()
            ->where('status', 'approved')
            ->whereNull('payout_id')
            ->update(['payout_id' => $payout->id]);

        return redirect()->route('affiliate.index')->with('status', __('messages.payout_requested'));
    }
}
