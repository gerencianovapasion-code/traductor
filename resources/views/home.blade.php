@extends('layouts.app')

@section('content')
<section class="hero container">
    <span class="badge">{{ __('messages.hero_badge') }}</span>
    <h1>{{ __('messages.hero_title') }}</h1>
    <p class="lead">{{ __('messages.hero_subtitle', ['count' => $languageCount]) }}</p>
    <div class="hero-actions">
        <a href="{{ route('translate') }}" class="btn btn-primary btn-lg">🎙️ {{ __('messages.start_free') }}</a>
        <a href="{{ route('pricing') }}" class="btn btn-ghost btn-lg">{{ __('messages.see_plans') }}</a>
    </div>
    <div class="flags">
        @foreach($languages as $l)<span title="{{ $l->name }}">{{ $l->flag }}</span>@endforeach
        <span class="muted" style="font-size:1rem;align-self:center">+{{ max(0,$languageCount-$languages->count()) }}</span>
    </div>
</section>

<section class="container">
    <div class="grid grid-3">
        <div class="card">
            <h3>🎧 {{ __('messages.feat_any_source_t') }}</h3>
            <p class="muted">{{ __('messages.feat_any_source_d') }}</p>
        </div>
        <div class="card">
            <h3>🌍 {{ __('messages.feat_detect_t') }}</h3>
            <p class="muted">{{ __('messages.feat_detect_d') }}</p>
        </div>
        <div class="card">
            <h3>📱 {{ __('messages.feat_devices_t') }}</h3>
            <p class="muted">{{ __('messages.feat_devices_d') }}</p>
        </div>
    </div>
</section>

<section class="container">
    <div class="card center" style="background:linear-gradient(135deg,rgba(91,140,255,.12),rgba(124,92,255,.12))">
        <h2>{{ __('messages.how_title') }}</h2>
        <div class="grid grid-3" style="margin-top:20px;text-align:left">
            <div><h3>1. {{ __('messages.step1_t') }}</h3><p class="muted">{{ __('messages.step1_d') }}</p></div>
            <div><h3>2. {{ __('messages.step2_t') }}</h3><p class="muted">{{ __('messages.step2_d') }}</p></div>
            <div><h3>3. {{ __('messages.step3_t') }}</h3><p class="muted">{{ __('messages.step3_d') }}</p></div>
        </div>
    </div>
</section>

<section class="container" id="plans">
    <h2 class="center">{{ __('messages.plans_title') }}</h2>
    <p class="center muted">{{ __('messages.plans_subtitle') }}</p>
    <div class="grid grid-3" style="margin-top:24px">
        @include('partials.plan-cards', ['plans' => $plans])
    </div>
</section>

<section class="container">
    <div class="card center">
        <h2>💸 {{ __('messages.affiliate_home_t') }}</h2>
        <p class="muted">{{ __('messages.affiliate_home_d', ['l1' => config('translator.affiliate.levels.1')]) }}</p>
        <a href="{{ route('register') }}" class="btn btn-accent btn-lg">{{ __('messages.become_affiliate') }}</a>
    </div>
</section>
@endsection
