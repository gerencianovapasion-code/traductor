@extends('layouts.app')
@section('content')
<section class="container">
    <div class="form-narrow card">
        <h1 class="center">{{ __('messages.login') }}</h1>
        <form method="POST" action="{{ route('login') }}">@csrf
            <label for="email">{{ __('messages.email') }}</label>
            <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus>
            @error('email')<div class="error">{{ $message }}</div>@enderror

            <label for="password">{{ __('messages.password') }}</label>
            <input id="password" name="password" type="password" required>

            <label class="toggle" style="margin-top:12px"><input type="checkbox" name="remember"> {{ __('messages.remember_me') }}</label>

            <button class="btn btn-primary btn-block" style="margin-top:14px">{{ __('messages.login') }}</button>
        </form>
        <p class="center muted" style="margin-top:14px">{{ __('messages.no_account') }} <a href="{{ route('register') }}">{{ __('messages.signup') }}</a></p>
    </div>
</section>
@endsection
