@extends('layouts.admin')
@section('content')
<h1>💸 {{ __('messages.affiliates') }}</h1>
<div class="grid grid-2">
    <div class="card stat"><div class="n">{{ number_format($pendingCents/100,2,',','.') }} €</div><div class="l">{{ __('messages.pending_commissions') }}</div></div>
    <div class="card stat"><div class="n">{{ number_format($approvedCents/100,2,',','.') }} €</div><div class="l">{{ __('messages.approved') }}</div></div>
</div>

<div class="card" style="margin-top:18px">
    <h3>{{ __('messages.payout_requests') }}</h3>
    @if($payouts->isEmpty())<p class="muted">{{ __('messages.no_payouts') }}</p>@else
    <table><thead><tr><th>{{ __('messages.user') }}</th><th>{{ __('messages.amount') }}</th><th>{{ __('messages.method') }}</th><th>{{ __('messages.destination') }}</th><th>{{ __('messages.status') }}</th><th></th></tr></thead><tbody>
    @foreach($payouts as $p)
        <tr><td>{{ $p->user->name }}</td><td>{{ number_format($p->amount_cents/100,2,',','.') }} €</td><td>{{ $p->method }}</td><td>{{ $p->destination }}</td>
        <td><span class="pill {{ $p->status==='paid'?'green':'amber' }}">{{ __('messages.status_'.$p->status) }}</span></td>
        <td>@if($p->status!=='paid')<form method="POST" action="{{ route('admin.affiliates.payout.pay',$p) }}">@csrf<button class="btn btn-accent">{{ __('messages.mark_paid') }}</button></form>@endif</td></tr>
    @endforeach
    </tbody></table>
    @endif
</div>

<div class="card" style="margin-top:18px">
    <h3>{{ __('messages.commissions') }}</h3>
    <table><thead><tr><th>{{ __('messages.date') }}</th><th>{{ __('messages.affiliate') }}</th><th>{{ __('messages.from') }}</th><th>L</th><th>{{ __('messages.amount') }}</th><th>{{ __('messages.status') }}</th><th></th></tr></thead><tbody>
    @foreach($commissions as $c)
        <tr><td>{{ $c->created_at->format('Y-m-d') }}</td><td>{{ optional($c->affiliate)->name }}</td><td>{{ optional($c->sourceUser)->name }}</td><td>L{{ $c->level }}</td><td>{{ number_format($c->amount_cents/100,2,',','.') }} €</td>
        <td><span class="pill {{ ['pending'=>'amber','approved'=>'blue','paid'=>'green','rejected'=>'red'][$c->status] ?? 'amber' }}">{{ __('messages.status_'.$c->status) }}</span></td>
        <td style="display:flex;gap:6px">
            @if($c->status==='pending')
                <form method="POST" action="{{ route('admin.affiliates.commission.approve',$c) }}">@csrf<button class="btn btn-accent">✓</button></form>
                <form method="POST" action="{{ route('admin.affiliates.commission.reject',$c) }}">@csrf<button class="btn btn-danger">✕</button></form>
            @endif
        </td></tr>
    @endforeach
    </tbody></table>
    <div style="margin-top:14px">{{ $commissions->links() }}</div>
</div>
@endsection
