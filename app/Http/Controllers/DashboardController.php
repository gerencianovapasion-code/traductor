<?php

namespace App\Http\Controllers;

use App\Models\TranslationSession;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $plan = $user->currentPlan();

        $secondsThisMonth = TranslationSession::where('user_id', $user->id)
            ->where('created_at', '>=', now()->startOfMonth())
            ->sum('seconds');

        return view('dashboard', [
            'plan' => $plan,
            'usedMinutes' => (int) ceil($secondsThisMonth / 60),
            'referralCount' => $user->referrals()->count(),
            'pendingCents' => (int) $user->commissions()->where('status', 'pending')->sum('amount_cents'),
            'approvedCents' => $user->availableBalanceCents(),
            'recent' => $user->translationSessions()->latest()->take(10)->get(),
        ]);
    }
}
