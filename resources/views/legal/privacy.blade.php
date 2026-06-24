@extends('layouts.app')
@section('content')
<section class="container" style="max-width:760px">
    <h1>{{ __('messages.privacy') }}</h1>
    <p class="muted">{{ __('messages.legal_updated') }}: {{ date('Y-m-d') }}</p>
    <p>{{ __('messages.privacy_intro') }}</p>
    <h3>{{ __('messages.privacy_data_t') }}</h3>
    <p>{{ __('messages.privacy_data_d') }}</p>
    <h3>{{ __('messages.privacy_audio_t') }}</h3>
    <p>{{ __('messages.privacy_audio_d') }}</p>
    <h3>{{ __('messages.privacy_contact_t') }}</h3>
    <p>{{ \App\Models\Setting::get('contact_email', 'gerencianovapasion@gmail.com') }}</p>
</section>
@endsection
