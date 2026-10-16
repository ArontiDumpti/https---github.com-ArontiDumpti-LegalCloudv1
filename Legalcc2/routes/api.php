<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\AssistantController;
use App\Http\Controllers\Api\V1\AuditController;
use App\Http\Controllers\Api\V1\CaseController;
use App\Http\Controllers\Api\V1\CategoryController;
use App\Http\Controllers\Api\V1\DashboardController;
use App\Http\Controllers\Api\V1\DocumentController;
use App\Http\Controllers\Api\V1\NotificationController;
use App\Http\Controllers\Api\V1\ProcessingJobController;
use App\Http\Controllers\Api\V1\UserController;

Route::get('/v1/openapi.yaml', function () {
    return response(file_get_contents(base_path('docs/openapi.yaml')), 200, [
        'Content-Type' => 'application/yaml; charset=UTF-8',
    ]);
})->name('api.v1.openapi');

Route::prefix('v1')->name('api.v1.')->group(function (): void {
    Route::post('/auth/login', [AuthController::class, 'login'])->middleware('throttle:login')->name('auth.login');

    Route::middleware(['auth:api', 'organization.active'])->group(function (): void {
        Route::post('/auth/logout', [AuthController::class, 'logout'])->name('auth.logout');

        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        Route::post('/users', [UserController::class, 'store'])->middleware('role:administrator')->name('users.store');
        Route::get('/users/{userId}', [UserController::class, 'show'])->name('users.show');
        Route::patch('/users/{userId}', [UserController::class, 'update'])->middleware('role:administrator')->name('users.update');

        Route::get('/cases', [CaseController::class, 'index'])->name('cases.index');
        Route::post('/cases', [CaseController::class, 'store'])->name('cases.store');
        Route::get('/cases/{caseId}', [CaseController::class, 'show'])->name('cases.show');
        Route::patch('/cases/{caseId}', [CaseController::class, 'update'])->name('cases.update');

        Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
        Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');

        Route::get('/documents', [DocumentController::class, 'index'])->name('documents.index');
        Route::post('/documents', [DocumentController::class, 'store'])->middleware('role:administrator,records_manager,analyst')->name('documents.store');
        Route::get('/documents/{documentId}', [DocumentController::class, 'show'])->name('documents.show');
        Route::patch('/documents/{documentId}', [DocumentController::class, 'update'])->middleware('role:administrator,records_manager,analyst')->name('documents.update');
        Route::delete('/documents/{documentId}', [DocumentController::class, 'destroy'])->middleware('role:administrator,records_manager,analyst')->name('documents.destroy');
        Route::get('/documents/{documentId}/versions', [DocumentController::class, 'versions'])->name('documents.versions.index');
        Route::post('/documents/{documentId}/versions', [DocumentController::class, 'storeVersion'])->middleware('role:administrator,records_manager,analyst')->name('documents.versions.store');
        Route::get('/documents/{documentId}/versions/{versionId}/download', [DocumentController::class, 'download'])->name('documents.versions.download');
        Route::post('/documents/{documentId}/ocr', [ProcessingJobController::class, 'startOcr'])->middleware('role:administrator,records_manager,analyst')->name('documents.ocr');
        Route::get('/processing-jobs/{jobId}', [ProcessingJobController::class, 'show'])->name('processing-jobs.show');

        Route::post('/search', [DocumentController::class, 'search'])->middleware('throttle:api')->name('search');
        Route::get('/assistant/conversations', [AssistantController::class, 'index'])->name('assistant.conversations.index');
        Route::post('/assistant/conversations', [AssistantController::class, 'store'])->name('assistant.conversations.store');
        Route::post('/assistant/conversations/{conversationId}/messages', [AssistantController::class, 'ask'])->middleware('throttle:api')->name('assistant.messages.store');
        Route::get('/assistant/messages/{messageId}', [AssistantController::class, 'showMessage'])->name('assistant.messages.show');

        Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
        Route::patch('/notifications/{notificationId}/read', [NotificationController::class, 'markRead'])->name('notifications.read');
        Route::get('/audit-events', [AuditController::class, 'index'])->middleware('role:administrator,records_manager')->name('audit.index');
        Route::get('/dashboard', [DashboardController::class, 'show'])->name('dashboard');
    });
});