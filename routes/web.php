<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\MeController;
use App\Http\Controllers\Transactions\TransactionsController;

Route::get('/', function () {
    return view('welcome');
});

Route::post('/auth/{provider}', [AuthController::class, 'postAuthenticate'])->name('authenticate');

Route::get('/users/me', [MeController::class, 'getMe'])->name('usersMe');

Route::post('/transactions', [TransactionsController::class, 'postTransaction'])->name('postTransaction');
