@extends('layouts.app')
@section('content')
<section class="container" style="max-width:760px">
    <h1>{{ __('messages.cookies') }}</h1>
    <p class="muted">{{ __('messages.legal_updated') }}: {{ date('Y-m-d') }}</p>
    <p>{{ __('messages.cookies_intro') }}</p>
    <ul>
        <li>{{ __('messages.cookies_session') }}</li>
        <li>{{ __('messages.cookies_locale') }}</li>
        <li>{{ __('messages.cookies_affiliate') }}</li>
    </ul>
</section>
@endsection
