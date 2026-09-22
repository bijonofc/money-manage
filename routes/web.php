<?php

use App\Http\Controllers\Api\TestMailController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

if (! function_exists('resolveAppLocale')) {
    function resolveAppLocale(): void {
        $locale = request()->cookie('app_locale', request()->query('locale', config('app.locale', 'en')));
        if (in_array($locale, ['en', 'bn'], true)) {
            app()->setLocale($locale);
        }
    }
}

Route::get('/set-locale/{locale}', function (string $locale, Request $request) {
    if (in_array($locale, ['en', 'bn'], true)) {
        cookie()->queue(cookie()->forever('app_locale', $locale));
    }
    return redirect()->back();
});

Route::get('/', function () {
    resolveAppLocale();
    return view('app');
});

Route::get('/test-email', [TestMailController::class, 'send']);

Route::get('/{any}', function () {
    resolveAppLocale();
    return view('app');
})->where('any', '.*');
