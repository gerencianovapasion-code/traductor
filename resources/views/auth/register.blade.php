@extends('layouts.app')
@section('content')
<section class="container">
    <div class="form-narrow card">
        <h1 class="center">{{ __('messages.signup') }}</h1>
        <p class="center muted">{{ __('messages.register_subtitle') }}</p>
        <form method="POST" action="{{ route('register') }}">@csrf
            <label for="name">{{ __('messages.name') }}</label>
            <input id="name" name="name" value="{{ old('name') }}" required autofocus>
            @error('name')<div class="error">{{ $message }}</div>@enderror

            <label for="email">{{ __('messages.email') }}</label>
            <input id="email" name="email" type="email" value="{{ old('email') }}" required>
            @error('email')<div class="error">{{ $message }}</div>@enderror

            <label for="password">{{ __('messages.password') }}</label>
            <input id="password" name="password" type="password" required>
            @error('password')<div class="error">{{ $message }}</div>@enderror

            <label for="password_confirmation">{{ __('messages.confirm_password') }}</label>
            <input id="password_confirmation" name="password_confirmation" type="password" required>

            <button class="btn btn-primary btn-block" style="margin-top:18px">{{ __('messages.create_account') }}</button>
        </form>
        <p class="center muted" style="margin-top:14px">{{ __('messages.have_account') }} <a href="{{ route('login') }}">{{ __('messages.login') }}</a></p>
    </div>
</section>
@endsection
