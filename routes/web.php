<?php

use App\Http\Controllers\SlackEventsController;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::post('/slack/events', SlackEventsController::class)
    ->withoutMiddleware(ValidateCsrfToken::class);
