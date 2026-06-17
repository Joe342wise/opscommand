<?php

use App\Http\Controllers\ActivityController;
use App\Http\Controllers\AttachmentController;
use App\Http\Controllers\AuditController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\EscalationController;
use App\Http\Controllers\HandoverController;
use App\Http\Controllers\IncidentController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PersonnelController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\ShiftController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WatchTeamController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard.index');
    }

    return redirect()->route('login');
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->middleware('permission:view_dashboard')
        ->name('dashboard.index');

    Route::resource('activities', ActivityController::class)
        ->except(['edit', 'update'])
        ->middleware('permission:manage_activities');
    Route::put('activities/{activity}', [ActivityController::class, 'update'])
        ->middleware('permission:update_activities')
        ->name('activities.update');
    Route::post('activities/{activity}/remark', [ActivityController::class, 'addRemark'])
        ->middleware('permission:update_activities')
        ->name('activities.remark');

    Route::resource('incidents', IncidentController::class)
        ->except(['edit'])
        ->middleware('permission:manage_incidents');
    Route::post('incidents/{incident}/note', [IncidentController::class, 'addNote'])
        ->middleware('permission:manage_incidents')
        ->name('incidents.note');
    Route::post('incidents/{incident}/resolve', [IncidentController::class, 'resolve'])
        ->middleware('permission:manage_incidents')
        ->name('incidents.resolve');

    Route::resource('escalations', EscalationController::class)
        ->except(['edit'])
        ->middleware('permission:escalate_incidents');

    Route::resource('handovers', HandoverController::class)
        ->except(['edit', 'destroy'])
        ->middleware('permission:manage_handovers');
    Route::post('handovers/{handover}/acknowledge', [HandoverController::class, 'acknowledge'])
        ->middleware('permission:manage_handovers')
        ->name('handovers.acknowledge');

    Route::prefix('audit')->name('audit.')->middleware('permission:view_audit_logs')->group(function () {
        Route::get('/', [AuditController::class, 'index'])->name('index');
        Route::get('/{auditLog}', [AuditController::class, 'show'])->name('show');
    });

    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/', [NotificationController::class, 'index'])->name('index');
        Route::post('/{recipient}/read', [NotificationController::class, 'markAsRead'])->name('mark-read');
        Route::post('/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('mark-all-read');
        Route::get('/alerts', [NotificationController::class, 'alerts'])->name('alerts');
        Route::post('/alerts/{alert}/acknowledge', [NotificationController::class, 'acknowledgeAlert'])->name('alerts.acknowledge');
        Route::post('/alerts/{alert}/resolve', [NotificationController::class, 'resolveAlert'])->name('alerts.resolve');
    });

    Route::resource('services', ServiceController::class)
        ->except(['edit'])
        ->middleware('permission:manage_services');
    Route::post('services/{service}/metric', [ServiceController::class, 'addMetric'])
        ->middleware('permission:manage_services')
        ->name('services.metric');

    Route::get('/watch-team', [WatchTeamController::class, 'index'])
        ->middleware('permission:view_dashboard')
        ->name('watch-team.index');

    Route::resource('users', UserController::class)
        ->middleware('permission:manage_users');

    Route::resource('personnel', PersonnelController::class)
        ->except(['edit', 'update'])
        ->middleware('permission:manage_users');
    Route::put('personnel/{personnel}', [PersonnelController::class, 'update'])
        ->middleware('permission:manage_users')
        ->name('personnel.update');

    Route::resource('shifts', ShiftController::class)
        ->middleware('permission:manage_users');

    Route::resource('departments', DepartmentController::class)
        ->middleware('permission:manage_users');

    Route::resource('teams', TeamController::class)
        ->middleware('permission:manage_users');

    Route::prefix('reports')->name('reports.')->middleware('permission:view_reports')->group(function () {
        Route::get('/', [ReportController::class, 'index'])->name('index');
        Route::get('/create', [ReportController::class, 'create'])->name('create');
        Route::get('/kpis', [ReportController::class, 'kpis'])->name('kpis');
        Route::post('/', [ReportController::class, 'store'])->name('store');
        Route::get('/{report}', [ReportController::class, 'show'])->name('show');
        Route::get('/{report}/export', [ReportController::class, 'export'])->name('export');
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

    Route::post('attachments', [AttachmentController::class, 'store'])->name('attachments.store');
    Route::delete('attachments/{attachment}', [AttachmentController::class, 'destroy'])->name('attachments.destroy');
    Route::get('attachments/{attachment}/download', [AttachmentController::class, 'download'])->name('attachments.download');
});

Route::middleware('guest')->prefix('mfa')->name('mfa.')->group(function () {
    Route::get('verify', [LoginController::class, 'showMfaForm'])->name('verify');
    Route::post('verify', [LoginController::class, 'verifyMfa']);
});
