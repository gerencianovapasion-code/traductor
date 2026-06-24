@extends('layouts.admin')
@section('content')
<h1>{{ __('messages.dashboard') }}</h1>
<div class="grid grid-4">
    <div class="card stat"><div class="n">{{ $users }}</div><div class="l">{{ __('messages.users') }}</div></div>
    <div class="card stat"><div class="n">{{ $activeSubs }}</div><div class="l">{{ __('messages.active_subs') }}</div></div>
    <div class="card stat"><div class="n">{{ number_format($mrrCents/100,2,',','.') }} €</div><div class="l">{{ __('messages.mrr') }}</div></div>
    <div class="card stat"><div class="n">{{ $minutesThisMonth }}</div><div class="l">{{ __('messages.minutes_month') }}</div></div>
</div>
<div class="card" style="margin-top:18px">
    <h3>{{ __('messages.latest_users') }}</h3>
    <table><thead><tr><th>{{ __('messages.name') }}</th><th>{{ __('messages.email') }}</th><th>{{ __('messages.role') }}</th><th>{{ __('messages.date') }}</th></tr></thead><tbody>
        @foreach($latestUsers as $u)<tr><td>{{ $u->name }}</td><td>{{ $u->email }}</td><td>{{ $u->role }}</td><td>{{ $u->created_at->format('Y-m-d') }}</td></tr>@endforeach
    </tbody></table>
</div>
<p class="muted" style="margin-top:14px">{{ __('messages.pending_commissions') }}: {{ number_format($pendingCommissionsCents/100,2,',','.') }} €</p>
@endsection
