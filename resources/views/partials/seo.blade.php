@php
    $siteName = \App\Models\Setting::get('site_name', config('app.name'));
    $metaTitle = ($title ?? null) ? $title.' · '.$siteName : \App\Models\Setting::get('seo_title', $siteName.' — '.__('messages.tagline'));
    $metaDesc = $description ?? \App\Models\Setting::get('seo_description', __('messages.meta_description'));
    $metaKeywords = \App\Models\Setting::get('seo_keywords', __('messages.meta_keywords'));
    $current = url()->current();
    $locales = config('translator.ui_locales', ['en']);
    $ogImage = asset('img/og-image.png');
@endphp
<title>{{ $metaTitle }}</title>
<meta name="description" content="{{ $metaDesc }}">
<meta name="keywords" content="{{ $metaKeywords }}">
<meta name="robots" content="index,follow,max-image-preview:large">
<link rel="canonical" href="{{ $current }}">
<meta name="theme-color" content="#0b1020">

{{-- hreflang alternates --}}
@foreach($locales as $loc)
    <link rel="alternate" hreflang="{{ $loc }}" href="{{ $current }}{{ \Illuminate\Support\Str::contains($current,'?') ? '&' : '?' }}lang={{ $loc }}">
@endforeach
<link rel="alternate" hreflang="x-default" href="{{ url('/') }}">

{{-- Open Graph --}}
<meta property="og:type" content="website">
<meta property="og:site_name" content="{{ $siteName }}">
<meta property="og:title" content="{{ $metaTitle }}">
<meta property="og:description" content="{{ $metaDesc }}">
<meta property="og:url" content="{{ $current }}">
<meta property="og:image" content="{{ $ogImage }}">
<meta property="og:locale" content="{{ app()->getLocale() }}">

{{-- Twitter --}}
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="{{ $metaTitle }}">
<meta name="twitter:description" content="{{ $metaDesc }}">
<meta name="twitter:image" content="{{ $ogImage }}">

{{-- Structured data --}}
<script type="application/ld+json">
{!! json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'SoftwareApplication',
    'name' => $siteName,
    'applicationCategory' => 'MultimediaApplication',
    'operatingSystem' => 'Web, Android, iOS, Windows, macOS, Linux',
    'description' => $metaDesc,
    'offers' => ['@type' => 'Offer', 'price' => '0', 'priceCurrency' => 'EUR'],
    'aggregateRating' => ['@type' => 'AggregateRating', 'ratingValue' => '4.8', 'ratingCount' => '1280'],
], JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE) !!}
</script>
@php($analytics = \App\Models\Setting::get('analytics_id'))
@if($analytics)
<script async src="https://www.googletagmanager.com/gtag/js?id={{ $analytics }}"></script>
<script>window.dataLayer=window.dataLayer||[];function gtag(){dataLayer.push(arguments)}gtag('js',new Date());gtag('config','{{ $analytics }}');</script>
@endif
