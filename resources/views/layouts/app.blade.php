<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    @include('partials.seo')
    <link rel="manifest" href="{{ asset('manifest.webmanifest') }}">
    <link rel="icon" href="{{ asset('img/icon.svg') }}" type="image/svg+xml">
    <link rel="apple-touch-icon" href="{{ asset('img/icon-192.png') }}">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}?v=1">
    @stack('head')
</head>
<body>
@php
    $uiLanguages = \App\Models\Language::ui()->get();
    $siteName = \App\Models\Setting::get('site_name', config('app.name'));
@endphp
<header class="site-header">
    <div class="container nav">
        <a href="{{ route('home') }}" class="brand">
            <span class="logo">🌐</span> {{ $siteName }}
        </a>
        <div class="spacer"></div>
        <a href="{{ route('translate') }}" class="navlink">{{ __('messages.nav_translate') }}</a>
        <a href="{{ route('features') }}" class="navlink">{{ __('messages.nav_features') }}</a>
        <a href="{{ route('pricing') }}" class="navlink">{{ __('messages.nav_pricing') }}</a>

        <details class="lang-switch">
            <summary>🌍 {{ strtoupper(app()->getLocale()) }}</summary>
            <div class="lang-menu">
                @foreach($uiLanguages as $lang)
                    <a href="{{ route('locale.switch', $lang->code) }}">{{ $lang->label() }}</a>
                @endforeach
            </div>
        </details>

        @auth
            <a href="{{ route('dashboard') }}" class="navlink">{{ __('messages.nav_dashboard') }}</a>
            @if(auth()->user()->isAdmin())
                <a href="{{ route('admin.dashboard') }}" class="navlink">Admin</a>
            @endif
            <form method="POST" action="{{ route('logout') }}" style="margin:0">@csrf
                <button class="btn btn-ghost">{{ __('messages.logout') }}</button>
            </form>
        @else
            <a href="{{ route('login') }}" class="navlink">{{ __('messages.login') }}</a>
            <a href="{{ route('register') }}" class="btn btn-primary">{{ __('messages.signup') }}</a>
        @endauth
    </div>
</header>

<main>
    @if(session('status'))
        <div class="container"><div class="alert alert-success">{{ session('status') }}</div></div>
    @endif
    @yield('content')
</main>

<footer class="site-footer">
    <div class="container foot-grid">
        <div>
            <div class="brand"><span class="logo">🌐</span> {{ $siteName }}</div>
            <p class="muted" style="max-width:320px">{{ __('messages.footer_tagline') }}</p>
        </div>
        <div>
            <strong>{{ __('messages.product') }}</strong><br>
            <a href="{{ route('translate') }}">{{ __('messages.nav_translate') }}</a><br>
            <a href="{{ route('features') }}">{{ __('messages.nav_features') }}</a><br>
            <a href="{{ route('pricing') }}">{{ __('messages.nav_pricing') }}</a>
        </div>
        <div>
            <strong>{{ __('messages.legal') }}</strong><br>
            <a href="{{ route('legal','privacy') }}">{{ __('messages.privacy') }}</a><br>
            <a href="{{ route('legal','terms') }}">{{ __('messages.terms') }}</a><br>
            <a href="{{ route('legal','cookies') }}">{{ __('messages.cookies') }}</a>
        </div>
        <div>
            <strong>{{ __('messages.earn') }}</strong><br>
            <a href="{{ route('register') }}">{{ __('messages.become_affiliate') }}</a>
        </div>
    </div>
    <div class="container muted" style="margin-top:18px">© {{ date('Y') }} {{ $siteName }}. {{ __('messages.rights') }}</div>
</footer>

<script>
    if ('serviceWorker' in navigator) {
        window.addEventListener('load', () => navigator.serviceWorker.register('{{ asset('sw.js') }}').catch(()=>{}));
    }
    window.APP = {
        csrf: '{{ csrf_token() }}',
        routes: {
            translate: '{{ route('api.translate') }}',
            detect: '{{ route('api.detect') }}',
            usage: '{{ route('api.usage') }}'
        }
    };
</script>
@stack('scripts')
</body>
</html>
