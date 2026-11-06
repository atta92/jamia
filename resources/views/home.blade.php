@extends('layouts.public')

@section('title', ($site['name'] ?? config('site.name')).' — صفحہ اول')
@section('meta_description', $site['short_description'] ?? config('site.short_description'))
@section('og_type', 'website')

@section('seo_json_ld')
@php
    $siteName = $site['name'] ?? config('site.name');
    $logo = ! empty($site['logo']) ? asset('storage/'.$site['logo']) : asset('images/logo-calligraphy.jpg');
    $jsonLd = [
        '@context' => 'https://schema.org',
        '@type' => 'EducationalOrganization',
        'name' => $siteName,
        'url' => url('/'),
        'logo' => $logo,
        'description' => $site['short_description'] ?? config('site.short_description'),
        'address' => [
            '@type' => 'PostalAddress',
            'addressLocality' => $site['prayer']['city_ur'] ?? ($site['prayer']['city'] ?? 'چکوال'),
            'addressCountry' => 'PK',
        ],
    ];
@endphp
<script type="application/ld+json">{!! json_encode($jsonLd, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES) !!}</script>
@endsection

@section('content')
    @include('partials.home.hero')
    @include('partials.home.durood-scroll')
    @include('partials.home.prayer-timing')
    @include('partials.home.gallery')
    @include('partials.home.blog')
    @include('partials.home.hifz')
    @include('partials.home.muhtamim-message')
@endsection
