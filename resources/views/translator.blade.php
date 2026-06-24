@extends('layouts.app')

@section('content')
<section class="container">
    <div class="translator">
        <h1 class="center">🎙️ {{ __('messages.translator_title') }}</h1>
        <p class="center muted">{{ __('messages.translator_subtitle') }}</p>

        <div class="card">
            <div class="lang-row">
                <div>
                    <label for="sourceLang">{{ __('messages.i_hear') }}</label>
                    <select id="sourceLang">
                        <option value="auto">🔎 {{ __('messages.auto_detect') }}</option>
                        @foreach($languages->where('can_listen', true) as $l)
                            <option value="{{ $l->speech_code ?: $l->code }}" data-code="{{ $l->code }}">{{ $l->flag }} {{ $l->native_name }}</option>
                        @endforeach
                    </select>
                </div>
                <button class="swap" id="swapBtn" title="{{ __('messages.swap') }}">⇄</button>
                <div>
                    <label for="targetLang">{{ __('messages.i_want_to_hear') }}</label>
                    <select id="targetLang">
                        @foreach($languages->where('can_speak', true) as $l)
                            <option value="{{ $l->code }}" data-speech="{{ $l->speech_code ?: $l->code }}" @selected($l->code===app()->getLocale())>{{ $l->flag }} {{ $l->native_name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="mic-wrap">
                <button id="micBtn" class="mic-btn" title="{{ __('messages.tap_to_speak') }}">🎙️</button>
                <div id="status" class="muted">{{ __('messages.idle') }}</div>
            </div>

            <div class="toggle">
                <input type="checkbox" id="autoSpeak" checked>
                <label for="autoSpeak" style="margin:0">🔊 {{ __('messages.auto_speak') }}</label>
            </div>

            <label class="muted">{{ __('messages.heard') }}</label>
            <div id="transcript" class="transcript"><span class="muted">…</span></div>

            <label class="muted" style="margin-top:14px">{{ __('messages.translation') }}</label>
            <div id="output" class="output"><span class="muted">…</span></div>
        </div>

        @guest
            <p class="notice" style="margin-top:16px">{{ __('messages.guest_quota_notice') }}
                <a href="{{ route('register') }}">{{ __('messages.signup') }}</a>.</p>
        @else
            @php($limit = $plan->minutes_limit)
            <p class="muted center" style="margin-top:14px">
                {{ __('messages.plan_label') }}: <strong>{{ $plan->name }}</strong> ·
                {{ __('messages.used') }}: {{ $usedMinutes }}{{ $limit ? '/'.$limit : '' }} {{ __('messages.minutes') }}
            </p>
            @if($limit && $usedMinutes >= $limit)
                <p class="notice">{{ __('messages.limit_reached') }} <a href="{{ route('subscription.index') }}">{{ __('messages.upgrade') }}</a>.</p>
            @endif
        @endguest

        <p id="unsupported" class="notice" style="display:none;margin-top:16px">{{ __('messages.unsupported_browser') }}</p>
    </div>
</section>
@endsection

@push('scripts')
<script>
    window.TRANSLATOR = {
        plan: @json(['engine' => optional($plan)->engine ?? 'browser', 'minutes' => optional($plan)->minutes_limit, 'used' => $usedMinutes ?? 0]),
        loggedIn: @json(auth()->check()),
        strings: {
            listening: @json(__('messages.listening')),
            idle: @json(__('messages.idle')),
            error: @json(__('messages.mic_error')),
        }
    };
</script>
<script src="{{ asset('js/translator.js') }}?v=1"></script>
@endpush
