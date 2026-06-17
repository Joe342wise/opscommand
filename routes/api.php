<?php

use App\Http\Controllers\Api\V1\ActivityController;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\EscalationController;
use App\Http\Controllers\Api\V1\HandoverController;
use App\Http\Controllers\Api\V1\IncidentController;
use App\Http\Controllers\Api\V1\NotificationController;
use App\Http\Controllers\Api\V1\ReportController;
use App\Http\Controllers\Api\V1\ServiceController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::prefix('auth')->group(function () {
        Route::post('login', [AuthController::class, 'login']);
        Route::post('forgot-password', [AuthController::class, 'forgotPassword']);
        Route::post('reset-password', [AuthController::class, 'resetPassword']);
        Route::post('mfa/verify', [AuthController::class, 'verifyMfa']);

        Route::middleware('auth:sanctum')->group(function () {
            Route::post('logout', [AuthController::class, 'logout']);
            Route::post('refresh', [AuthController::class, 'refresh']);
        });
    });

    Route::middleware('auth:sanctum')->group(function () {
        Route::apiResource('activities', ActivityController::class);
        Route::post('activities/{activity}/remarks', [ActivityController::class, 'addRemark']);

        Route::apiResource('incidents', IncidentController::class);
        Route::post('incidents/{incident}/notes', [IncidentController::class, 'addNote']);
        Route::post('incidents/{incident}/resolve', [IncidentController::class, 'resolve']);

        Route::apiResource('escalations', EscalationController::class);
        Route::post('escalations/{escalation}/close', [EscalationController::class, 'close']);

        Route::apiResource('handovers', HandoverController::class);
        Route::post('handovers/{handover}/items', [HandoverController::class, 'addItem']);
        Route::post('handovers/{handover}/acknowledge', [HandoverController::class, 'acknowledge']);

        Route::get('notifications', [NotificationController::class, 'index']);
        Route::post('notifications/read-all', [NotificationController::class, 'markAllAsRead']);
        Route::post('notifications/{recipient}/read', [NotificationController::class, 'markAsRead']);

        Route::get('alerts', [NotificationController::class, 'alerts']);
        Route::post('alerts', [NotificationController::class, 'storeAlert']);
        Route::post('alerts/{alert}/acknowledge', [NotificationController::class, 'acknowledgeAlert']);
        Route::post('alerts/{alert}/resolve', [NotificationController::class, 'resolveAlert']);
        Route::delete('alerts/{alert}', [NotificationController::class, 'destroyAlert']);

        Route::get('services/stats', [ServiceController::class, 'stats']);
        Route::apiResource('services', ServiceController::class);
        Route::post('services/{service}/metrics', [ServiceController::class, 'addMetric']);

        Route::get('reports/kpis', [ReportController::class, 'kpis']);
        Route::apiResource('reports', ReportController::class);
    });
});
