@extends('layouts.admin')
@section('content')
<h1>{{ $plan->exists ? __('messages.edit').': '.$plan->name : __('messages.new_plan') }}</h1>
<div class="card" style="max-width:620px">
<form method="POST" action="{{ $plan->exists ? route('admin.plans.update',$plan) : route('admin.plans.store') }}">@csrf
    @if($plan->exists) @method('PUT') @endif
    <div class="grid grid-2">
        <div><label>{{ __('messages.name') }}</label><input name="name" value="{{ old('name',$plan->name) }}" required></div>
        <div><label>Slug</label><input name="slug" value="{{ old('slug',$plan->slug) }}" required></div>
        <div><label>{{ __('messages.level') }} (1-3)</label><input name="level" type="number" min="1" max="3" value="{{ old('level',$plan->level) }}" required></div>
        <div><label>{{ __('messages.price') }} (céntimos)</label><input name="price_cents" type="number" min="0" value="{{ old('price_cents',$plan->price_cents) }}" required></div>
        <div><label>{{ __('messages.currency') }}</label><input name="currency" value="{{ old('currency',$plan->currency ?? 'EUR') }}" maxlength="3" required></div>
        <div><label>{{ __('messages.interval') }}</label><select name="interval"><option value="month" @selected($plan->interval==='month')>month</option><option value="year" @selected($plan->interval==='year')>year</option><option value="lifetime" @selected($plan->interval==='lifetime')>lifetime</option></select></div>
        <div><label>{{ __('messages.minutes') }} ({{ __('messages.blank_unlimited') }})</label><input name="minutes_limit" type="number" min="0" value="{{ old('minutes_limit',$plan->minutes_limit) }}"></div>
        <div><label>{{ __('messages.engine') }}</label><select name="engine"><option value="browser" @selected($plan->engine==='browser')>browser</option><option value="cloud" @selected($plan->engine==='cloud')>cloud</option></select></div>
    </div>
    <label class="toggle"><input type="hidden" name="allow_system_audio" value="0"><input type="checkbox" name="allow_system_audio" value="1" @checked($plan->allow_system_audio)> {{ __('messages.allow_system_audio') }}</label>
    <label class="toggle"><input type="hidden" name="ads" value="0"><input type="checkbox" name="ads" value="1" @checked($plan->ads)> {{ __('messages.show_ads') }}</label>
    <label class="toggle"><input type="hidden" name="is_active" value="0"><input type="checkbox" name="is_active" value="1" @checked($plan->is_active ?? true)> {{ __('messages.active') }}</label>
    <label>{{ __('messages.features') }} ({{ __('messages.one_per_line') }})</label>
    <textarea name="features" rows="6">{{ old('features', implode("\n", $plan->features ?? [])) }}</textarea>
    <label>{{ __('messages.sort') }}</label><input name="sort" type="number" value="{{ old('sort',$plan->sort ?? 0) }}">
    <button class="btn btn-primary" style="margin-top:14px">{{ __('messages.save') }}</button>
    <a href="{{ route('admin.plans.index') }}" class="btn btn-ghost">{{ __('messages.cancel') }}</a>
</form>
</div>
@endsection
