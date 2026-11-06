<!DOCTYPE html>
<html lang="ur" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', $site['name'] ?? config('site.name'))</title>
    <meta name="description" content="{{ $site['short_description'] ?? config('site.short_description') }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@500;600;700;800&family=Epilogue:wght@400;600;700;800;900&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body @class([
    'min-h-screen font-urdu',
    'page-solid-header' => ! empty($solidHeader),
    'has-page-fade' => ! request()->routeIs('home'),
])>
    @include('partials.header')

    <main @class(['site-main', 'site-main--offset' => ! empty($solidHeader)])>
        @yield('content')
    </main>

    @include('partials.footer')
    @include('partials.float-actions')
</body>
</html>
