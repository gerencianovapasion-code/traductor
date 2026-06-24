@extends('layouts.app')
@section('content')
<section class="container">
    <h1 class="center">{{ __('messages.plans_title') }}</h1>
    <p class="center muted">{{ __('messages.plans_subtitle') }}</p>
    <div class="grid grid-3" style="margin-top:26px">
        @include('partials.plan-cards', ['plans' => $plans])
    </div>
    <div class="card center" style="margin-top:30px">
        <h2>💸 {{ __('messages.affiliate_home_t') }}</h2>
        <p class="muted">{{ __('messages.affiliate_home_d', ['l1' => config('translator.affiliate.levels.1')]) }}</p>
        <a href="{{ route('register') }}" class="btn btn-accent">{{ __('messages.become_affiliate') }}</a>
    </div>
</section>
@endsection
