@extends('layouts.app')
@section('content')
<section class="container" style="max-width:640px">
    <h1>{{ __('messages.settings') }}</h1>

    <div class="card">
        <h3>{{ __('messages.profile') }}</h3>
        <form method="POST" action="{{ route('profile.update') }}">@csrf @method('PUT')
            <label for="name">{{ __('messages.name') }}</label>
            <input id="name" name="name" value="{{ old('name',$user->name) }}" required>
            <label for="email">{{ __('messages.email') }}</label>
            <input id="email" name="email" type="email" value="{{ old('email',$user->email) }}" required>
            @error('email')<div class="error">{{ $message }}</div>@enderror

            <label for="locale">{{ __('messages.ui_language') }}</label>
            <select id="locale" name="locale">
                @foreach(\App\Models\Language::ui()->get() as $l)
                    <option value="{{ $l->code }}" @selected($user->locale===$l->code)>{{ $l->label() }}</option>
                @endforeach
            </select>

            <div class="grid grid-2">
                <div>
                    <label for="default_source_lang">{{ __('messages.default_source') }}</label>
                    <select id="default_source_lang" name="default_source_lang">
                        <option value="">{{ __('messages.auto_detect') }}</option>
                        @foreach(\App\Models\Language::active()->get() as $l)
                            <option value="{{ $l->code }}" @selected($user->default_source_lang===$l->code)>{{ $l->flag }} {{ $l->native_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="default_target_lang">{{ __('messages.default_target') }}</label>
                    <select id="default_target_lang" name="default_target_lang">
                        @foreach(\App\Models\Language::active()->get() as $l)
                            <option value="{{ $l->code }}" @selected($user->default_target_lang===$l->code)>{{ $l->flag }} {{ $l->native_name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <button class="btn btn-primary" style="margin-top:16px">{{ __('messages.save') }}</button>
        </form>
    </div>

    <div class="card" style="margin-top:18px">
        <h3>{{ __('messages.change_password') }}</h3>
        <form method="POST" action="{{ route('profile.password') }}">@csrf @method('PUT')
            <label for="current_password">{{ __('messages.current_password') }}</label>
            <input id="current_password" name="current_password" type="password" required>
            @error('current_password')<div class="error">{{ $message }}</div>@enderror
            <label for="password">{{ __('messages.new_password') }}</label>
            <input id="password" name="password" type="password" required>
            @error('password')<div class="error">{{ $message }}</div>@enderror
            <label for="password_confirmation">{{ __('messages.confirm_password') }}</label>
            <input id="password_confirmation" name="password_confirmation" type="password" required>
            <button class="btn btn-ghost" style="margin-top:16px">{{ __('messages.update_password') }}</button>
        </form>
    </div>
</section>
@endsection
