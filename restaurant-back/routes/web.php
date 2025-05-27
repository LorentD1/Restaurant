<?php

use Illuminate\Support\Facades\Route;
use Laravel\Sanctum\Http\Controllers\CsrfCookieController;

Route::get('/login', function () {
    return response()->json(['message' => 'Unauthorized.'], 401);
})->name('login');


Route::get('/sanctum/csrf-cookie', [CsrfCookieController::class, 'show']);
