<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin · {{ \App\Models\Setting::get('site_name', config('app.name')) }}</title>
    <meta name="robots" content="noindex,nofollow">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}?v=1">
</head>
<body>
<div class="admin-wrap">
    <aside class="admin-side">
        <div class="brand" style="margin-bottom:18px"><span class="logo">🌐</span> Admin</div>
        @php($r = request()->route()->getName())
        <a href="{{ route('admin.dashboard') }}" class="{{ $r==='admin.dashboard'?'active':'' }}">📊 {{ __('messages.dashboard') }}</a>
        <a href="{{ route('admin.users.index') }}" class="{{ str_starts_with($r,'admin.users')?'active':'' }}">👤 {{ __('messages.users') }}</a>
        <a href="{{ route('admin.plans.index') }}" class="{{ str_starts_with($r,'admin.plans')?'active':'' }}">💳 {{ __('messages.plans') }}</a>
        <a href="{{ route('admin.languages.index') }}" class="{{ str_starts_with($r,'admin.languages')?'active':'' }}">🌍 {{ __('messages.languages') }}</a>
        <a href="{{ route('admin.affiliates.index') }}" class="{{ str_starts_with($r,'admin.affiliates')?'active':'' }}">💸 {{ __('messages.affiliates') }}</a>
        <a href="{{ route('admin.settings.index') }}" class="{{ str_starts_with($r,'admin.settings')?'active':'' }}">⚙️ {{ __('messages.settings') }}</a>
        <a href="{{ route('home') }}" style="margin-top:18px">← {{ __('messages.back_to_site') }}</a>
    </aside>
    <main class="admin-main">
        @if(session('status'))<div class="alert alert-success">{{ session('status') }}</div>@endif
        @yield('content')
    </main>
</div>
</body>
</html>
