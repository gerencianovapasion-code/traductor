@extends('layouts.app')
@section('content')
<section class="container" style="max-width:760px">
    <h1>{{ __('messages.terms') }}</h1>
    <p class="muted">{{ __('messages.legal_updated') }}: {{ date('Y-m-d') }}</p>
    <p>{{ __('messages.terms_intro') }}</p>
    <h3>{{ __('messages.terms_use_t') }}</h3>
    <p>{{ __('messages.terms_use_d') }}</p>
    <h3>{{ __('messages.terms_billing_t') }}</h3>
    <p>{{ __('messages.terms_billing_d') }}</p>
    <h3>{{ __('messages.terms_affiliate_t') }}</h3>
    <p>{{ __('messages.terms_affiliate_d') }}</p>
</section>
@endsection
