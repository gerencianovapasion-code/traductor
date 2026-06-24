@extends('layouts.admin')
@section('content')
<h1>{{ __('messages.users') }}</h1>
<form method="GET" style="max-width:340px;margin-bottom:14px">
    <input name="q" value="{{ request('q') }}" placeholder="{{ __('messages.search') }}…">
</form>
<div class="card">
<table>
    <thead><tr><th>#</th><th>{{ __('messages.name') }}</th><th>{{ __('messages.email') }}</th><th>{{ __('messages.role') }}</th><th>{{ __('messages.plan_label') }}</th><th>{{ __('messages.referrals') }}</th><th></th></tr></thead>
    <tbody>
    @foreach($users as $u)
        <tr>
            <td>{{ $u->id }}</td>
            <td>{{ $u->name }}</td>
            <td>{{ $u->email }}</td>
            <td>@if($u->isAdmin())<span class="pill blue">admin</span>@else user @endif</td>
            <td>{{ $u->currentPlan()->name }}</td>
            <td>{{ $u->referrals_count }}</td>
            <td><a href="{{ route('admin.users.edit',$u) }}" class="btn btn-ghost">{{ __('messages.edit') }}</a></td>
        </tr>
    @endforeach
    </tbody>
</table>
</div>
<div style="margin-top:16px">{{ $users->links() }}</div>
@endsection
