<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Commission;
use App\Models\Subscription;
use App\Models\TranslationSession;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        return view('admin.dashboard', [
            'users' => User::count(),
            'activeSubs' => Subscription::where('status', 'active')->count(),
            'mrrCents' => (int) Subscription::where('status', 'active')->sum('amount_cents'),
            'minutesThisMonth' => (int) ceil(
                TranslationSession::where('created_at', '>=', now()->startOfMonth())->sum('seconds') / 60
            ),
            'pendingCommissionsCents' => (int) Commission::where('status', 'pending')->sum('amount_cents'),
            'latestUsers' => User::latest()->take(10)->get(),
        ]);
    }
}
