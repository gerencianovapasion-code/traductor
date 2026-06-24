@extends('layouts.admin')
@section('content')
<h1>{{ __('messages.edit') }}: {{ $user->name }}</h1>
<div class="card" style="max-width:560px">
<form method="POST" action="{{ route('admin.users.update',$user) }}">@csrf @method('PUT')
    <label>{{ __('messages.name') }}</label>
    <input name="name" value="{{ old('name',$user->name) }}" required>
    <label>{{ __('messages.email') }}</label>
    <input name="email" type="email" value="{{ old('email',$user->email) }}" required>
    <label>{{ __('messages.role') }}</label>
    <select name="role"><option value="user" @selected($user->role==='user')>user</option><option value="admin" @selected($user->role==='admin')>admin</option></select>
    <label>{{ __('messages.grant_plan') }}</label>
    <select name="plan_id"><option value="">— {{ __('messages.no_change') }} —</option>
        @foreach($plans as $p)<option value="{{ $p->id }}">{{ $p->name }}</option>@endforeach
    </select>
    <p class="help">{{ __('messages.grant_plan_help') }}</p>
    <button class="btn btn-primary" style="margin-top:14px">{{ __('messages.save') }}</button>
    <a href="{{ route('admin.users.index') }}" class="btn btn-ghost">{{ __('messages.cancel') }}</a>
</form>
</div>
@endsection
