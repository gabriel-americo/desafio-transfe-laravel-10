<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\MeController;

Route::get('/', function () {
    return view('welcome');
});

Route::post('/auth/{provider}', [AuthController::class, 'postAuthenticate'])->name('authenticate');

Route::get('/users/me', [MeController::class, 'getMe'])->name('usersMe');
