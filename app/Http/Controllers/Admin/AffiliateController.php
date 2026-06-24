<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Commission;
use App\Models\Payout;
use Illuminate\Http\Request;

class AffiliateController extends Controller
{
    public function index()
    {
        return view('admin.affiliates.index', [
            'commissions' => Commission::with(['affiliate', 'sourceUser'])->latest()->paginate(30),
            'payouts' => Payout::with('user')->latest()->take(50)->get(),
            'pendingCents' => (int) Commission::where('status', 'pending')->sum('amount_cents'),
            'approvedCents' => (int) Commission::where('status', 'approved')->sum('amount_cents'),
        ]);
    }

    public function approveCommission(Commission $commission)
    {
        if ($commission->status === 'pending') {
            $commission->update(['status' => 'approved']);
        }

        return back()->with('status', __('messages.saved'));
    }

    public function rejectCommission(Commission $commission)
    {
        $commission->update(['status' => 'rejected']);

        return back()->with('status', __('messages.saved'));
    }

    public function payPayout(Payout $payout)
    {
        $payout->update(['status' => 'paid', 'paid_at' => now()]);
        Commission::where('payout_id', $payout->id)->update(['status' => 'paid']);

        return back()->with('status', __('messages.saved'));
    }
}
