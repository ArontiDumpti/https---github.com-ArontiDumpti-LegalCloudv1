<?php

use App\Http\Controllers\Api\V1\AssistantController;
use App\Http\Controllers\Api\V1\CaseController;
use App\Http\Controllers\Api\V1\CategoryController;
use App\Http\Controllers\Api\V1\DocumentController;
use App\Http\Controllers\Api\V1\NotificationController;
use App\Http\Controllers\Api\V1\ProcessingJobController;
use App\Http\Controllers\Api\V1\UserController;
use App\Http\Controllers\Web\PageController;
use App\Http\Controllers\Web\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', [PageController::class, 'home']);
Route::get('/dashboard', [PageController::class, 'dashboard']);
Route::get('/usuarios', [PageController::class, 'users']);
Route::get('/categorias', [PageController::class, 'categories']);
Route::get('/expedientes', [PageController::class, 'cases']);
Route::get('/expedientes/{expediente}', [PageController::class, 'cases']);
Route::get('/documentos', [PageController::class, 'documents']);
Route::get('/documentos/{documento}', [PageController::class, 'documents']);
Route::get('/busqueda', [PageController::class, 'search']);
Route::get('/asistente', [PageController::class, 'assistant']);
Route::get('/ocr', [PageController::class, 'ocr']);
Route::get('/notificaciones', [PageController::class, 'notifications']);
Route::get('/settings/profile', [PageController::class, 'profile']);
Route::get('/settings/password', [PageController::class, 'password']);
Route::get('/settings/appearance', [PageController::class, 'appearance']);
Route::any('/settings', [PageController::class, 'settings']);

Route::middleware(['auth:api', 'organization.active'])->group(function (): void {
    Route::post('/usuarios', [UserController::class, 'store'])->middleware('role:administrator');
    Route::post('/categorias', [CategoryController::class, 'store']);
    Route::post('/expedientes', [CaseController::class, 'store']);
    Route::post('/documentos', [DocumentController::class, 'store'])->middleware('role:administrator,records_manager,analyst');
    Route::post('/documentos/ejemplo', [DocumentController::class, 'storeExample'])->middleware('role:administrator,records_manager,analyst');
    Route::get('/documentos/{documentId}/archivo', [DocumentController::class, 'downloadCurrent']);
    Route::post('/ocr/{documentId}', [ProcessingJobController::class, 'startOcr'])->middleware('role:administrator,records_manager,analyst');
    Route::post('/asistente', [AssistantController::class, 'query'])->middleware('throttle:api');
    Route::post('/notificaciones/{notificationId}/leer', [NotificationController::class, 'markRead']);
    Route::patch('/settings/profile', [ProfileController::class, 'update']);
    Route::delete('/settings/profile', [ProfileController::class, 'destroy']);
    Route::put('/settings/password', [ProfileController::class, 'updatePassword']);
});
