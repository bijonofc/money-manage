<!DOCTYPE html>
<html lang="{{app()->getLocale()}}" style="height: 100%;">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @if(app()->isProduction() && request()->isSecure())
    <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">
    @endif
    <title>Money Manage</title>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Bengali:wght@300;400;500;700&display=swap" rel="stylesheet">
    @if(file_exists(public_path('favicon/site.webmanifest')))
    <link rel="manifest" href="{{ asset('favicon/site.webmanifest') }}">
    @endif
    <meta name="theme-color" content="#4f46e5">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-title" content="Money Manage">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <link rel="icon" type="image/png" href="{{ asset('logo/logo.png') }}">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">
    <link rel="apple-touch-icon" href="{{ asset('logo/logo.png') }}">

    {{-- Preload Brand Logos for instant zero-flicker rendering --}}
    <link rel="preload" href="{{ asset('logo/logo.png') }}" as="image" type="image/png">
    <link rel="preload" href="{{ asset('logo/moneymanage.png') }}" as="image" type="image/png">

    @if(file_exists(public_path('font/style.css')))
    <link rel="stylesheet" href="{{ asset('font/style.css') }}"/>
    @endif

    <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>

    @php
        $langFile = 'lang/' . app()->getLocale() . '.js';
        $langVersion = file_exists(public_path($langFile)) ? filemtime(public_path($langFile)) : time();
        $langUrl = asset($langFile) . '?v=' . $langVersion;
    @endphp

    <link rel="preload" href="{{ $langUrl }}" as="script">
    <script src="{{ $langUrl }}"></script>

    <script>
        window.app_settings = {
            base_url:"{{ url('/') }}",
            api_url:"{{ url('/api/v1') }}/",
            currencySymbol:"{{env('CURRENCY_SYMBOL','৳')}}",
            locale:"{{app()->getLocale()}}", // here need a add function to convert php date time format to javascript function.
            site_key:"{{env('TURNSTILE_SITE_KEY','')}}", // here need a add function to convert php date time format to javascript function.
            gl_client_id:"383506021268-b016dcou1r2g9bhmauffdcvftm84es8t.apps.googleusercontent.com", // here need a add function to convert php date time format to javascript function.
            is_prod: {{ app()->isProduction() ? 'true' : 'false' }},
            app_env: "{{ app()->environment() }}"
        };
        let app_settings = window.app_settings;
        window.appType = 'admin';
    </script>
    <style>
        #app {
            --bs-font-sans-serif: 'Noto Sans Bengali', 'Hind Siliguri', 'SolaimanLipi', 'Kalpurush', sans-serif,system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", "Noto Sans", "Liberation Sans", Arial, sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol", "Noto Color Emoji";
            font-family: var(--bs-body-font-family);
            line-height: 1.6;
            min-height: 100vh;
            height: 100dvh;
            overflow-y: auto;
        }

    </style>
    @vite('resources/js/app.js')
</head>
<body>
    <div id="app" class="ab-app"></div>
</body>
</html>
