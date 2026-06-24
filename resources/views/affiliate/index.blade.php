@extends('layouts.app')
@section('content')
<section class="container">
    <h1>💸 {{ __('messages.affiliate_panel') }}</h1>

    <div class="card">
        <h3>{{ __('messages.your_link') }}</h3>
        <p class="muted">{{ __('messages.affiliate_explainer', ['l1'=>$rates[1],'l2'=>$rates[2],'l3'=>$rates[3]]) }}</p>
        <div style="display:flex;gap:10px;flex-wrap:wrap">
            <input id="reflink" value="{{ $link }}" readonly style="flex:1;min-width:240px">
            <button class="btn btn-primary" onclick="navigator.clipboard.writeText(document.getElementById('reflink').value);this.textContent='✓'">{{ __('messages.copy') }}</button>
        </div>
        <p class="help" style="margin-top:8px">{{ __('messages.your_code') }}: <strong>{{ $code }}</strong></p>
    </div>

    <div class="grid grid-4" style="margin-top:18px">
        <div class="card stat"><div class="n">{{ $referralCount }}</div><div class="l">{{ __('messages.referrals') }}</div></div>
        <div class="card stat"><div class="n">{{ number_format($pendingCents/100,2,',','.') }} €</div><div class="l">{{ __('messages.pending') }}</div></div>
        <div class="card stat"><div class="n">{{ number_format($approvedCents/100,2,',','.') }} €</div><div class="l">{{ __('messages.available') }}</div></div>
        <div class="card stat"><div class="n">{{ number_format($paidCents/100,2,',','.') }} €</div><div class="l">{{ __('messages.paid') }}</div></div>
    </div>

    <div class="grid grid-2" style="margin-top:18px">
        <div class="card">
            <h3>{{ __('messages.request_payout') }}</h3>
            <p class="muted">{{ __('messages.min_payout') }}: {{ number_format($minPayout/100,2,',','.') }} €</p>
            @error('amount')<div class="error">{{ $message }}</div>@enderror
            <form method="POST" action="{{ route('affiliate.payout') }}">@csrf
                <label for="method">{{ __('messages.method') }}</label>
                <select id="method" name="method"><option value="paypal">PayPal</option><option value="bank">{{ __('messages.bank') }}</option></select>
                <label for="destination">{{ __('messages.destination') }}</label>
                <input id="destination" name="destination" placeholder="email@paypal / IBAN" required>
                <button class="btn btn-accent btn-block" style="margin-top:14px" {{ $approvedCents < $minPayout ? 'disabled' : '' }}>{{ __('messages.request_payout') }}</button>
            </form>
        </div>
        <div class="card">
            <h3>{{ __('messages.payout_history') }}</h3>
            @if($payouts->isEmpty())<p class="muted">{{ __('messages.no_payouts') }}</p>@else
            <table><thead><tr><th>{{ __('messages.date') }}</th><th>{{ __('messages.amount') }}</th><th>{{ __('messages.status') }}</th></tr></thead><tbody>
                @foreach($payouts as $p)<tr><td>{{ $p->created_at->format('Y-m-d') }}</td><td>{{ number_format($p->amount_cents/100,2,',','.') }} €</td><td><span class="pill {{ $p->status==='paid'?'green':'amber' }}">{{ __('messages.status_'.$p->status) }}</span></td></tr>@endforeach
            </tbody></table>
            @endif
        </div>
    </div>

    <div class="card" style="margin-top:18px">
        <h3>{{ __('messages.your_commissions') }}</h3>
        @if($commissions->isEmpty())<p class="muted">{{ __('messages.no_commissions') }}</p>@else
        <table><thead><tr><th>{{ __('messages.date') }}</th><th>{{ __('messages.level') }}</th><th>{{ __('messages.from') }}</th><th>{{ __('messages.amount') }}</th><th>{{ __('messages.status') }}</th></tr></thead><tbody>
            @foreach($commissions as $c)
                <tr><td>{{ $c->created_at->format('Y-m-d') }}</td><td>L{{ $c->level }} ({{ $c->rate }}%)</td><td>{{ optional($c->sourceUser)->name ?? '—' }}</td><td>{{ number_format($c->amount_cents/100,2,',','.') }} €</td><td><span class="pill {{ ['pending'=>'amber','approved'=>'blue','paid'=>'green','rejected'=>'red'][$c->status] ?? 'amber' }}">{{ __('messages.status_'.$c->status) }}</span></td></tr>
            @endforeach
        </tbody></table>
        @endif
    </div>
</section>
@endsection
