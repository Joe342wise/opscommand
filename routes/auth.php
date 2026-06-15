<?php

use App\Http\Controllers\Auth\LoginController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('login', [LoginController::class, 'login']);
    Route::get('forgot-password', fn () => view('auth.forgot-password'))->name('password.request');
    Route::get('reset-password/{token}', fn () => view('auth.reset-password'))->name('password.reset');
});

Route::post('logout', [LoginController::class, 'logout'])->middleware('auth')->name('logout');
