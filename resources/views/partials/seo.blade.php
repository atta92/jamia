@php
    $siteName = $site['name'] ?? config('site.name');
    $defaultDescription = \Illuminate\Support\Str::of($site['short_description'] ?? config('site.short_description', ''))
        ->stripTags()
        ->squish()
        ->limit(160)
        ->toString();

    $seoTitle = trim($__env->yieldContent('title', $siteName));
    $seoDescription = \Illuminate\Support\Str::of(trim($__env->yieldContent('meta_description', $defaultDescription)) ?: $defaultDescription)
        ->stripTags()
        ->squish()
        ->limit(160)
        ->toString();
    $seoCanonical = trim($__env->yieldContent('canonical', url()->current())) ?: url()->current();
    $seoType = trim($__env->yieldContent('og_type', 'website')) ?: 'website';
    $seoRobots = trim($__env->yieldContent('robots', 'index,follow')) ?: 'index,follow';

    $logoPath = ! empty($site['logo']) ? asset('storage/'.$site['logo']) : asset('images/logo-calligraphy.jpg');
    $heroPath = ! empty($site['hero_image']) ? asset('storage/'.$site['hero_image']) : null;
    $seoImage = trim($__env->yieldContent('og_image', $heroPath ?: $logoPath)) ?: $logoPath;
@endphp

<title>{{ $seoTitle }}</title>
<meta name="description" content="{{ $seoDescription }}">
<meta name="robots" content="{{ $seoRobots }}">
<meta name="author" content="{{ $siteName }}">
<link rel="canonical" href="{{ $seoCanonical }}">

{{-- Icons / branding --}}
<link rel="icon" href="{{ asset('favicon.ico') }}" sizes="any">
<link rel="icon" type="image/jpeg" href="{{ $logoPath }}">
<link rel="apple-touch-icon" href="{{ $logoPath }}">
<link rel="shortcut icon" href="{{ asset('favicon.ico') }}">
<meta name="theme-color" content="#0b6b5e">
<meta name="msapplication-TileColor" content="#0b6b5e">
<meta name="application-name" content="{{ $siteName }}">
<meta name="apple-mobile-web-app-title" content="{{ $siteName }}">

{{-- Open Graph --}}
<meta property="og:locale" content="ur_PK">
<meta property="og:site_name" content="{{ $siteName }}">
<meta property="og:type" content="{{ $seoType }}">
<meta property="og:title" content="{{ $seoTitle }}">
<meta property="og:description" content="{{ $seoDescription }}">
<meta property="og:url" content="{{ $seoCanonical }}">
<meta property="og:image" content="{{ $seoImage }}">
<meta property="og:image:alt" content="{{ $seoTitle }}">

{{-- Twitter --}}
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="{{ $seoTitle }}">
<meta name="twitter:description" content="{{ $seoDescription }}">
<meta name="twitter:image" content="{{ $seoImage }}">
<meta name="twitter:image:alt" content="{{ $seoTitle }}">

@hasSection('seo_json_ld')
    @yield('seo_json_ld')
@endif
