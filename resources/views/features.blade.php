@extends('layouts.app')
@section('content')
<section class="container">
    <h1 class="center">{{ __('messages.features_title') }}</h1>
    <div class="grid grid-2" style="margin-top:24px">
        <div class="card"><h3>🎧 {{ __('messages.feat_any_source_t') }}</h3><p class="muted">{{ __('messages.feat_any_source_d') }}</p></div>
        <div class="card"><h3>🌍 {{ __('messages.feat_detect_t') }}</h3><p class="muted">{{ __('messages.feat_detect_d') }}</p></div>
        <div class="card"><h3>📱 {{ __('messages.feat_devices_t') }}</h3><p class="muted">{{ __('messages.feat_devices_d') }}</p></div>
        <div class="card"><h3>🔊 {{ __('messages.feat_tts_t') }}</h3><p class="muted">{{ __('messages.feat_tts_d') }}</p></div>
        <div class="card"><h3>🛡️ {{ __('messages.feat_privacy_t') }}</h3><p class="muted">{{ __('messages.feat_privacy_d') }}</p></div>
        <div class="card"><h3>💸 {{ __('messages.feat_affiliate_t') }}</h3><p class="muted">{{ __('messages.feat_affiliate_d') }}</p></div>
    </div>
    <div class="center" style="margin-top:30px">
        <a href="{{ route('translate') }}" class="btn btn-primary btn-lg">{{ __('messages.start_free') }}</a>
    </div>
</section>
@endsection
