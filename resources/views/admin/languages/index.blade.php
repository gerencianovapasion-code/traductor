@extends('layouts.admin')
@section('content')
<h1>{{ __('messages.languages') }} ({{ $languages->count() }})</h1>
<p class="muted">{{ __('messages.languages_help') }}</p>
<div class="card">
<table>
    <thead><tr><th>{{ __('messages.language') }}</th><th>{{ __('messages.code') }}</th><th>STT</th><th>TTS</th><th>UI</th><th>{{ __('messages.active') }}</th><th></th></tr></thead>
    <tbody>
    @foreach($languages as $l)
        <form method="POST" action="{{ route('admin.languages.update',$l) }}">@csrf @method('PUT')
        <tr>
            <td>{{ $l->flag }} {{ $l->native_name }} <span class="muted">({{ $l->name }})</span></td>
            <td>{{ $l->code }} <span class="muted">{{ $l->speech_code }}</span></td>
            <td><input type="hidden" name="can_listen" value="0"><input type="checkbox" name="can_listen" value="1" @checked($l->can_listen)></td>
            <td><input type="hidden" name="can_speak" value="0"><input type="checkbox" name="can_speak" value="1" @checked($l->can_speak)></td>
            <td><input type="hidden" name="ui" value="0"><input type="checkbox" name="ui" value="1" @checked($l->ui)></td>
            <td><input type="hidden" name="is_active" value="0"><input type="checkbox" name="is_active" value="1" @checked($l->is_active)></td>
            <td><button class="btn btn-ghost">{{ __('messages.save') }}</button></td>
        </tr>
        </form>
    @endforeach
    </tbody>
</table>
</div>
@endsection
