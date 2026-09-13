<?php

use App\Http\Controllers\Api\TestMailController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('app');
});

Route::get('/test-email', [TestMailController::class, 'send']);

Route::get('/{any}', function () {
    return view('app');
})->where('any', '.*');

