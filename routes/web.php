<?php

use App\Http\Controllers\ActivityController;
use App\Http\Controllers\AuditController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EscalationController;
use App\Http\Controllers\HandoverController;
use App\Http\Controllers\IncidentController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard.index');
    }
    return redirect()->route('login');
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');

    Route::resource('activities', ActivityController::class)->except(['edit', 'update']);
    Route::put('activities/{activity}', [ActivityController::class, 'update'])->name('activities.update');
    Route::post('activities/{activity}/remark', [ActivityController::class, 'addRemark'])->name('activities.remark');

    Route::resource('incidents', IncidentController::class)->except(['edit']);
    Route::post('incidents/{incident}/note', [IncidentController::class, 'addNote'])->name('incidents.note');
    Route::post('incidents/{incident}/resolve', [IncidentController::class, 'resolve'])->name('incidents.resolve');

    Route::resource('escalations', EscalationController::class)->except(['edit']);

    Route::resource('handovers', HandoverController::class)->except(['edit']);
    Route::post('handovers/{handover}/acknowledge', [HandoverController::class, 'acknowledge'])->name('handovers.acknowledge');

    Route::prefix('audit')->name('audit.')->group(function () {
        Route::get('/', [AuditController::class, 'index'])->name('index');
        Route::get('/{auditLog}', [AuditController::class, 'show'])->name('show');
    });

    Route::prefix('services')->name('services.')->group(function () {
        Route::get('/', fn () => view('dashboard.index'))->name('index');
    });

    Route::prefix('watch-team')->name('watch-team.')->group(function () {
        Route::get('/', fn () => view('dashboard.index'))->name('index');
    });

    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/', fn () => view('dashboard.index'))->name('index');
    });
});

Route::middleware('guest')->group(function () {
    Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('login', [LoginController::class, 'login']);
    Route::get('forgot-password', fn () => view('auth.forgot-password'))->name('password.request');
    Route::post('forgot-password', [LoginController::class, 'sendResetLinkEmail'])->name('password.email');
    Route::get('reset-password/{token}', [LoginController::class, 'showResetForm'])->name('password.reset');
    Route::post('reset-password', [LoginController::class, 'reset'])->name('password.update');
});

Route::middleware('auth')->group(function () {
    Route::post('logout', [LoginController::class, 'logout'])->name('logout');
});

Route::middleware('guest')->prefix('mfa')->name('mfa.')->group(function () {
    Route::get('verify', [LoginController::class, 'showMfaForm'])->name('verify');
    Route::post('verify', [LoginController::class, 'verifyMfa']);
});
