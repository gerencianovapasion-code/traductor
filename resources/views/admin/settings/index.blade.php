@extends('layouts.admin')
@section('content')
<h1>⚙️ {{ __('messages.settings') }}</h1>
<div class="card" style="max-width:620px">
<form method="POST" action="{{ route('admin.settings.update') }}">@csrf @method('PUT')
    <label>{{ __('messages.site_name') }}</label><input name="site_name" value="{{ $settings['site_name'] }}">
    <label>SEO title</label><input name="seo_title" value="{{ $settings['seo_title'] }}">
    <label>SEO description</label><textarea name="seo_description" rows="2">{{ $settings['seo_description'] }}</textarea>
    <label>SEO keywords</label><input name="seo_keywords" value="{{ $settings['seo_keywords'] }}">
    <label>{{ __('messages.contact_email') }}</label><input name="contact_email" value="{{ $settings['contact_email'] }}">
    <label>Google Analytics ID</label><input name="analytics_id" value="{{ $settings['analytics_id'] }}" placeholder="G-XXXXXXX">
    <button class="btn btn-primary" style="margin-top:14px">{{ __('messages.save') }}</button>
</form>
</div>
@endsection
