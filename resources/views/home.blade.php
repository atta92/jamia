@extends('layouts.public')

@section('title', ($site['name'] ?? config('site.name')) . ' — صفحہ اول')

@section('content')
    @include('partials.home.hero')
    @include('partials.home.durood-scroll')
    @include('partials.home.prayer-timing')
    @include('partials.home.gallery')
    @include('partials.home.blog')
    @include('partials.home.hifz')
    @include('partials.home.muhtamim-message')
@endsection
