@extends('layouts.app')
@section('content')
<section class="container">
    <h1>{{ __('messages.nav_dashboard') }}</h1>
    <div class="grid grid-4">
        <div class="card stat"><div class="n">{{ $plan->name }}</div><div class="l">{{ __('messages.plan_label') }}</div></div>
        <div class="card stat"><div class="n">{{ $usedMinutes }}{{ $plan->minutes_limit ? '/'.$plan->minutes_limit : '' }}</div><div class="l">{{ __('messages.minutes_month') }}</div></div>
        <div class="card stat"><div class="n">{{ $referralCount }}</div><div class="l">{{ __('messages.referrals') }}</div></div>
        <div class="card stat"><div class="n">{{ number_format(($pendingCents+$approvedCents)/100,2,',','.') }} €</div><div class="l">{{ __('messages.earnings') }}</div></div>
    </div>

    <div class="grid grid-2" style="margin-top:18px">
        <div class="card">
            <h3>🎙️ {{ __('messages.quick_translate') }}</h3>
            <p class="muted">{{ __('messages.quick_translate_d') }}</p>
            <a href="{{ route('translate') }}" class="btn btn-primary">{{ __('messages.open_translator') }}</a>
        </div>
        <div class="card">
            <h3>💸 {{ __('messages.affiliate_panel') }}</h3>
            <p class="muted">{{ __('messages.affiliate_panel_d') }}</p>
            <a href="{{ route('affiliate.index') }}" class="btn btn-accent">{{ __('messages.go_affiliate') }}</a>
        </div>
    </div>

    <div class="card" style="margin-top:18px">
        <h3>{{ __('messages.recent_activity') }}</h3>
        @if($recent->isEmpty())
            <p class="muted">{{ __('messages.no_activity') }}</p>
        @else
        <table>
            <thead><tr><th>{{ __('messages.date') }}</th><th>{{ __('messages.source') }}</th><th>{{ __('messages.target') }}</th><th>{{ __('messages.minutes') }}</th></tr></thead>
            <tbody>
            @foreach($recent as $s)
                <tr><td>{{ $s->created_at->format('Y-m-d H:i') }}</td><td>{{ $s->source_lang ?? '—' }}</td><td>{{ $s->target_lang }}</td><td>{{ ceil($s->seconds/60) }}</td></tr>
            @endforeach
            </tbody>
        </table>
        @endif
    </div>

    <p style="margin-top:18px"><a href="{{ route('profile.edit') }}" class="navlink">⚙️ {{ __('messages.settings') }}</a> · <a href="{{ route('subscription.index') }}" class="navlink">💳 {{ __('messages.membership') }}</a></p>
</section>
@endsection
