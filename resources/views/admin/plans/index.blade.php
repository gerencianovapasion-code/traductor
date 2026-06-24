@extends('layouts.admin')
@section('content')
<h1>{{ __('messages.plans') }}</h1>
<a href="{{ route('admin.plans.create') }}" class="btn btn-primary" style="margin-bottom:14px">+ {{ __('messages.new_plan') }}</a>
<div class="card">
<table>
    <thead><tr><th>{{ __('messages.name') }}</th><th>{{ __('messages.level') }}</th><th>{{ __('messages.price') }}</th><th>{{ __('messages.minutes') }}</th><th>{{ __('messages.engine') }}</th><th>{{ __('messages.status') }}</th><th></th></tr></thead>
    <tbody>
    @foreach($plans as $p)
        <tr>
            <td>{{ $p->name }}</td><td>{{ $p->level }}</td>
            <td>{{ number_format($p->price_cents/100,2,',','.') }} {{ $p->currency }}</td>
            <td>{{ $p->minutes_limit ?? '∞' }}</td><td>{{ $p->engine }}</td>
            <td>@if($p->is_active)<span class="pill green">{{ __('messages.active') }}</span>@else<span class="pill red">off</span>@endif</td>
            <td><a href="{{ route('admin.plans.edit',$p) }}" class="btn btn-ghost">{{ __('messages.edit') }}</a></td>
        </tr>
    @endforeach
    </tbody>
</table>
</div>
@endsection
