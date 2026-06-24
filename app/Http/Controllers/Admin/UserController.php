<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $users = User::query()
            ->when($request->q, fn ($q) => $q->where('name', 'like', "%{$request->q}%")
                ->orWhere('email', 'like', "%{$request->q}%"))
            ->withCount('referrals')
            ->latest()
            ->paginate(25)
            ->withQueryString();

        return view('admin.users.index', ['users' => $users]);
    }

    public function edit(User $user)
    {
        return view('admin.users.edit', [
            'user' => $user,
            'plans' => Plan::orderBy('sort')->get(),
        ]);
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email,'.$user->id],
            'role' => ['required', 'in:user,admin'],
        ]);

        $user->update($data);

        // Optionally grant a plan directly from the admin.
        if ($request->filled('plan_id')) {
            $plan = Plan::findOrFail($request->plan_id);

            $user->subscriptions()->where('status', 'active')
                ->update(['status' => 'cancelled', 'cancelled_at' => now()]);

            if (! $plan->isFree()) {
                Subscription::create([
                    'user_id' => $user->id,
                    'plan_id' => $plan->id,
                    'status' => 'active',
                    'gateway' => 'admin',
                    'amount_cents' => 0, // granted, no commission
                    'currency' => $plan->currency,
                    'starts_at' => now(),
                    'ends_at' => now()->addMonth(),
                ]);
            }
        }

        return redirect()->route('admin.users.index')->with('status', __('messages.saved'));
    }
}
