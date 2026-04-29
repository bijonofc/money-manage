<!DOCTYPE html>
<html lang="{{app()->getLocale()}}" style="overflow: hidden;height: 100%;">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Money Manage</title>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Bengali:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link rel="manifest" href="{{ asset('favicon/site.webmanifest') }}">
    <meta name="theme-color" content="#4f46e5">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-title" content="NogorPOS">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <link rel="shortcut icon" href="{{ asset('favicon/favicon.ico') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon/favicon-16x16.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon/favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="192x192" href="{{ asset('favicon/android-chrome-192x192.png') }}">
    <link rel="icon" type="image/png" sizes="512x512" href="{{ asset('favicon/android-chrome-512x512.png') }}">

    <link rel="stylesheet" href="{{ asset('font/style.css') }}"/>

    <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>

    @php
        $langFile = 'lang/' . app()->getLocale() . '.js';
        $langVersion = file_exists(public_path($langFile)) ? filemtime(public_path($langFile)) : time();
        $langUrl = asset($langFile) . '?v=' . $langVersion;
    @endphp

    <link rel="preload" href="{{ $langUrl }}" as="script">
    <script src="{{ $langUrl }}"></script>

    <script>
        let app_settings={
            base_url:"{{ url('/') }}",
            api_url:"{{ url('/api/admin/v1') }}/",
            currencySymbol:"{{env('CURRENCY_SYMBOL','৳')}}",
            locale:"{{app()->getLocale()}}", // here need a add function to convert php date time format to javascript function.
            site_key:"{{env('TURNSTILE_SITE_KEY','')}}", // here need a add function to convert php date time format to javascript function.
            gl_client_id:"383506021268-b016dcou1r2g9bhmauffdcvftm84es8t.apps.googleusercontent.com", // here need a add function to convert php date time format to javascript function.
        };
        window.appType = 'admin';
    </script>
    <style>
        #app {
            --bs-font-sans-serif: 'Noto Sans Bengali', 'Hind Siliguri', 'SolaimanLipi', 'Kalpurush', sans-serif,system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", "Noto Sans", "Liberation Sans", Arial, sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol", "Noto Color Emoji";
            font-family: var(--bs-body-font-family);
            line-height: 1.6;
            height: 100dvh;
        }

    </style>
    @vite('resources/js/app.js')
</head>
<body>
    <div id="app" class="ab-app"></div>
</body>
</html>
