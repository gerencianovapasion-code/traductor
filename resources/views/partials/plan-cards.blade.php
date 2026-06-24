@foreach($plans as $plan)
    <div class="card plan {{ $plan->slug === 'premium' ? 'popular' : '' }}">
        @if($plan->slug === 'premium')<span class="tag">{{ __('messages.popular') }}</span>@endif
        <h3>{{ $plan->name }}</h3>
        <div class="price">
            @if($plan->isFree())
                {{ __('messages.free') }}
            @else
                {{ number_format($plan->price_cents/100, 2, ',', '.') }} €<small>/{{ __('messages.interval_'.$plan->interval) }}</small>
            @endif
        </div>
        <ul>
            @foreach(($plan->features ?? []) as $f)<li>{{ $f }}</li>@endforeach
        </ul>
        @auth
            <form method="POST" action="{{ route('subscription.subscribe', $plan) }}">@csrf
                <button class="btn {{ $plan->slug==='premium' ? 'btn-primary' : 'btn-ghost' }} btn-block">
                    {{ $plan->isFree() ? __('messages.current_or_free') : __('messages.choose_plan') }}
                </button>
            </form>
        @else
            <a href="{{ route('register') }}" class="btn {{ $plan->slug==='premium' ? 'btn-primary' : 'btn-ghost' }} btn-block">
                {{ $plan->isFree() ? __('messages.start_free') : __('messages.choose_plan') }}
            </a>
        @endauth
    </div>
@endforeach
