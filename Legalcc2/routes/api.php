<?php

use App\Http\Controllers\Api\V1\AssistantController;
use App\Http\Controllers\Api\V1\AuditController;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\CaseController;
use App\Http\Controllers\Api\V1\CategoryController;
use App\Http\Controllers\Api\V1\DashboardController;
use App\Http\Controllers\Api\V1\DocumentController;
use App\Http\Controllers\Api\V1\NotificationController;
use App\Http\Controllers\Api\V1\ProcessingJobController;
use App\Http\Controllers\Api\V1\RoleController;
use App\Http\Controllers\Api\V1\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/v1/openapi.yaml', function () {
    return response(file_get_contents(base_path('docs/openapi.yaml')), 200, [
        'Content-Type' => 'application/yaml; charset=UTF-8',
    ]);
})->name('api.v1.openapi');

Route::prefix('v1')->name('api.v1.')->group(function (): void {
    Route::post('/auth/login', [AuthController::class, 'login'])->middleware('throttle:login')->name('auth.login');
    Route::post('/auth/iniciar-sesion', [AuthController::class, 'login'])->middleware('throttle:login')->name('auth.iniciar-sesion');
    Route::post('/auth/registrar', [AuthController::class, 'register'])->middleware('throttle:login')->name('auth.registrar');

    Route::middleware(['auth:api', 'organization.active'])->group(function (): void {
        Route::post('/auth/logout', [AuthController::class, 'logout'])->name('auth.logout');
        Route::post('/auth/cerrar-sesion', [AuthController::class, 'logout'])->name('auth.cerrar-sesion');

        Route::get('/users', [UserController::class, 'index'])->middleware('role:administrator')->name('users.index');
        Route::post('/users', [UserController::class, 'store'])->middleware('role:administrator')->name('users.store');
        Route::get('/users/{userId}', [UserController::class, 'show'])->middleware('role:administrator')->name('users.show');
        Route::patch('/users/{userId}', [UserController::class, 'update'])->middleware('role:administrator')->name('users.update');
        Route::delete('/users/{userId}', [UserController::class, 'destroy'])->middleware('role:administrator')->name('users.destroy');

        $users = 'role:administrator';
        Route::get('/usuarios', [UserController::class, 'index'])->middleware($users)->name('usuarios.index');
        Route::post('/usuarios', [UserController::class, 'store'])->middleware($users)->name('usuarios.store');
        Route::get('/usuarios/{userId}', [UserController::class, 'show'])->middleware($users)->name('usuarios.show');
        Route::patch('/usuarios/{userId}', [UserController::class, 'update'])->middleware($users)->name('usuarios.update');
        Route::post('/usuarios/{userId}/suspender', [UserController::class, 'suspend'])->middleware($users)->name('usuarios.suspend');
        Route::post('/usuarios/{userId}/reactivar', [UserController::class, 'reactivate'])->middleware($users)->name('usuarios.reactivate');
        Route::put('/usuarios/{userId}/roles', [UserController::class, 'syncRoles'])->middleware($users)->name('usuarios.roles');

        Route::get('/roles', [RoleController::class, 'index'])->name('roles.index');
        Route::get('/permisos', [RoleController::class, 'permissions'])->name('permisos.index');
        Route::get('/roles/{roleId}', [RoleController::class, 'show'])->name('roles.show');
        Route::patch('/roles/{roleId}', [RoleController::class, 'update'])->middleware($users)->name('roles.update');
        Route::put('/roles/{roleId}/permisos', [RoleController::class, 'syncPermissions'])->middleware($users)->name('roles.permisos');

        Route::get('/cases', [CaseController::class, 'index'])->name('cases.index');
        Route::post('/cases', [CaseController::class, 'store'])->name('cases.store');
        Route::get('/cases/{caseId}', [CaseController::class, 'show'])->name('cases.show');
        Route::patch('/cases/{caseId}', [CaseController::class, 'update'])->name('cases.update');

        Route::get('/expedientes', [CaseController::class, 'index'])->name('expedientes.index');
        Route::post('/expedientes', [CaseController::class, 'store'])->name('expedientes.store');
        Route::get('/expedientes/{caseId}', [CaseController::class, 'show'])->name('expedientes.show');
        Route::patch('/expedientes/{caseId}', [CaseController::class, 'update'])->name('expedientes.update');
        Route::get('/expedientes/{caseId}/documentos', [CaseController::class, 'documents'])->name('expedientes.documentos');
        Route::post('/expedientes/{caseId}/documentos', [DocumentController::class, 'beginUpload'])->middleware('role:administrator,records_manager,analyst')->name('expedientes.documentos.iniciar');
        Route::post('/expedientes/{caseId}/documentos/archivo', [DocumentController::class, 'storeInCase'])->middleware('role:administrator,records_manager,analyst')->name('expedientes.documentos.archivo');
        Route::get('/expedientes/{caseId}/participantes', [CaseController::class, 'participants'])->name('expedientes.participantes.index');
        Route::post('/expedientes/{caseId}/participantes', [CaseController::class, 'storeParticipant'])->name('expedientes.participantes.store');
        Route::delete('/expedientes/{caseId}/participantes/{userId}', [CaseController::class, 'destroyParticipant'])->name('expedientes.participantes.destroy');
        Route::post('/expedientes/{caseId}/transiciones', [CaseController::class, 'transition'])->name('expedientes.transiciones');
        Route::get('/expedientes/{caseId}/trazabilidad', [CaseController::class, 'trace'])->name('expedientes.trazabilidad');

        Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
        Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');
        Route::get('/categorias', [CategoryController::class, 'index'])->name('categorias.index');
        Route::post('/categorias', [CategoryController::class, 'store'])->name('categorias.store');
        Route::get('/categorias/{categoryId}', [CategoryController::class, 'show'])->name('categorias.show');
        Route::patch('/categorias/{categoryId}', [CategoryController::class, 'update'])->name('categorias.update');
        Route::post('/categorias/{categoryId}/desactivar', [CategoryController::class, 'deactivate'])->name('categorias.desactivar');

        $writers = 'role:administrator,records_manager,analyst';
        Route::get('/documents', [DocumentController::class, 'index'])->name('documents.index');
        Route::post('/documents', [DocumentController::class, 'store'])->middleware($writers)->name('documents.store');
        Route::get('/documents/{documentId}', [DocumentController::class, 'show'])->name('documents.show');
        Route::patch('/documents/{documentId}', [DocumentController::class, 'update'])->middleware($writers)->name('documents.update');
        Route::delete('/documents/{documentId}', [DocumentController::class, 'destroy'])->middleware($writers)->name('documents.destroy');
        Route::get('/documents/{documentId}/versions', [DocumentController::class, 'versions'])->name('documents.versions.index');
        Route::post('/documents/{documentId}/versions', [DocumentController::class, 'storeVersion'])->middleware($writers)->name('documents.versions.store');
        Route::get('/documents/{documentId}/versions/{versionId}/download', [DocumentController::class, 'download'])->name('documents.versions.download');
        Route::post('/documents/{documentId}/ocr', [ProcessingJobController::class, 'startOcr'])->middleware($writers)->name('documents.ocr');
        Route::get('/processing-jobs/{jobId}', [ProcessingJobController::class, 'show'])->name('processing-jobs.show');

        Route::post('/documentos/archivo', [DocumentController::class, 'store'])->middleware($writers)->name('documentos.archivo');
        Route::put('/cargas/{token}', [DocumentController::class, 'receiveUpload'])->middleware($writers)->name('cargas.recibir');
        Route::get('/documentos', [DocumentController::class, 'index'])->name('documentos.index');
        Route::get('/documentos/{documentId}', [DocumentController::class, 'show'])->name('documentos.show');
        Route::patch('/documentos/{documentId}', [DocumentController::class, 'update'])->middleware($writers)->name('documentos.update');
        Route::post('/documentos/{documentId}/archivar', [DocumentController::class, 'archive'])->middleware($writers)->name('documentos.archivar');
        Route::get('/documentos/{documentId}/versiones', [DocumentController::class, 'versions'])->name('documentos.versiones.index');
        Route::post('/documentos/{documentId}/versiones', [DocumentController::class, 'storeVersion'])->middleware($writers)->name('documentos.versiones.store');
        Route::get('/documentos/{documentId}/versiones/{versionId}', [DocumentController::class, 'showVersion'])->name('documentos.versiones.show');
        Route::post('/documentos/{documentId}/versiones/{versionId}/confirmar', [DocumentController::class, 'confirmVersion'])->middleware($writers)->name('documentos.versiones.confirmar');
        Route::get('/documentos/{documentId}/versiones/{versionId}/descarga', [DocumentController::class, 'download'])->name('documentos.versiones.descarga');
        Route::post('/documentos/{documentId}/versiones/{versionId}/restaurar', [DocumentController::class, 'restoreVersion'])->middleware($writers)->name('documentos.versiones.restaurar');
        Route::get('/documentos/{documentId}/versiones/{versionId}/texto', [DocumentController::class, 'versionText'])->name('documentos.versiones.texto');
        Route::post('/documentos/{documentId}/versiones/{versionId}/procesamiento', [ProcessingJobController::class, 'reprocess'])->middleware($writers)->name('documentos.procesamiento');
        Route::get('/trabajos/{jobId}', [ProcessingJobController::class, 'show'])->name('trabajos.show');
        Route::post('/ingestions', [ProcessingJobController::class, 'ingest'])->middleware($writers)->name('ingestions.store');

        Route::post('/search', [DocumentController::class, 'search'])->middleware('throttle:api')->name('search');
        Route::get('/busquedas', [DocumentController::class, 'savedSearches'])->name('busquedas.index');
        Route::post('/busquedas', [DocumentController::class, 'storeSavedSearch'])->middleware('throttle:api')->name('busquedas.store');
        Route::get('/busquedas/{searchId}', [DocumentController::class, 'showSavedSearch'])->name('busquedas.show');

        Route::get('/assistant/conversations', [AssistantController::class, 'index'])->name('assistant.conversations.index');
        Route::post('/assistant/conversations', [AssistantController::class, 'store'])->name('assistant.conversations.store');
        Route::post('/assistant/conversations/{conversationId}/messages', [AssistantController::class, 'ask'])->middleware('throttle:api')->name('assistant.messages.store');
        Route::get('/assistant/messages/{messageId}', [AssistantController::class, 'showMessage'])->name('assistant.messages.show');

        Route::get('/conversaciones', [AssistantController::class, 'index'])->name('conversaciones.index');
        Route::post('/conversaciones', [AssistantController::class, 'store'])->name('conversaciones.store');
        Route::get('/conversaciones/{conversationId}', [AssistantController::class, 'show'])->name('conversaciones.show');
        Route::post('/conversaciones/{conversationId}/mensajes', [AssistantController::class, 'ask'])->middleware('throttle:api')->name('conversaciones.mensajes');
        Route::post('/queries', [AssistantController::class, 'query'])->middleware('throttle:api')->name('queries.store');
        Route::post('/resumenes', [AssistantController::class, 'summarize'])->name('resumenes.store');
        Route::get('/resumenes/{summaryId}', [AssistantController::class, 'showSummary'])->name('resumenes.show');

        Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
        Route::patch('/notifications/{notificationId}/read', [NotificationController::class, 'markRead'])->name('notifications.read');
        Route::get('/notificaciones', [NotificationController::class, 'index'])->name('notificaciones.index');
        Route::get('/notificaciones/no-leidas/conteo', [NotificationController::class, 'unreadCount'])->name('notificaciones.conteo');
        Route::post('/notificaciones/lectura', [NotificationController::class, 'markAllRead'])->name('notificaciones.lectura');
        Route::post('/notificaciones/{notificationId}/lectura', [NotificationController::class, 'markRead'])->name('notificaciones.leer');

        Route::get('/audit-events', [AuditController::class, 'index'])->middleware('role:administrator,records_manager')->name('audit.index');
        Route::get('/dashboard', [DashboardController::class, 'show'])->name('dashboard');
    });
});
