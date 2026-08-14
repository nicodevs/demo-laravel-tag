<?php

use App\Http\Controllers\SlackEventsController;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::post('/slack/events', SlackEventsController::class)
    ->withoutMiddleware(PreventRequestForgery::class);
