@extends('layouts.app')
@section('content')
<section class="container">
    <h1>{{ __('messages.membership') }}</h1>
    @if($current && $current->plan)
        <div class="card">
            <p>{{ __('messages.current_plan') }}: <strong>{{ $current->plan->name }}</strong>
                <span class="pill green">{{ __('messages.status_'.$current->status) }}</span></p>
            @if($current->ends_at)<p class="muted">{{ __('messages.renews') }}: {{ $current->ends_at->format('Y-m-d') }}</p>@endif
            <form method="POST" action="{{ route('subscription.cancel') }}" onsubmit="return confirm('{{ __('messages.confirm_cancel') }}')">@csrf
                <button class="btn btn-danger">{{ __('messages.cancel_membership') }}</button>
            </form>
        </div>
    @endif

    <h2 style="margin-top:24px">{{ __('messages.change_plan') }}</h2>
    <div class="grid grid-3" style="margin-top:14px">
        @include('partials.plan-cards', ['plans' => $plans])
    </div>
    <p class="notice" style="margin-top:18px">{{ __('messages.billing_note') }}</p>
</section>
@endsection
