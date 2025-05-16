<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Emprendedor\EmprendedorController;

use App\Http\Controllers\Emprendedor\{
    DashboardController as EmprendedorDashboardController,
    ExperienceController,
    PackageController,
    ReservationController,
    CalendarController,
    MessageController,
    PromotionController,
    BlogController,
    ProfileController,
    ConfigController as EmprendedorConfigController,
    ReservacionesController,
    ServicioController
};
use App\Http\Controllers\Superadmin\{
    DashboardController as SuperadminDashboardController,
    UserManagementController,
    CompanyManagementController,
    ContentManagementController,
    ExperienceManagementController,
    ReportController,
    ConfigController as SuperadminConfigController,
    SecurityController,
    SuperadminController,
    PortalController,
    PortalDesignController
};
use App\Http\Controllers\Turista\ReservaPublicaController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\Auth\GoogleController;

// Rutas públicas para autenticación
Route::prefix('auth')->group(function () {
    Route::post('/register-turista', [AuthController::class, 'registerTurista']);
    Route::post('/login', [AuthController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/user', [AuthController::class, 'user']);
    });
});

// Rutas públicas para poblar selectores
Route::get('locations', [LocationController::class, 'index']);
Route::get('categories', [CategoryController::class, 'index']);

// Recuperación de contraseña
Route::post('/forgot-password', [PasswordResetController::class, 'sendResetLinkEmail']);
Route::post('/reset-password', [PasswordResetController::class, 'reset']);

// Google authentication
Route::get('auth/google', [GoogleController::class, 'redirectToGoogle']);
Route::get('auth/google/callback', [GoogleController::class, 'handleGoogleCallback']);

// Rutas protegidas para SUPERADMIN
Route::middleware(['auth:sanctum', 'role:superadmin'])->prefix('superadmin')->group(function () {

    // Gestión general
    Route::get('dashboard', [SuperadminDashboardController::class, 'overview']);
    Route::apiResource('users', UserManagementController::class);
    Route::get('companies/pending', [CompanyManagementController::class, 'pending']);
    Route::put('companies/{id}/approve', [CompanyManagementController::class, 'approve']);
    Route::put('companies/{id}/reject', [CompanyManagementController::class, 'reject']);

    Route::apiResource('contents', ContentManagementController::class)
        ->only(['index', 'show', 'update', 'destroy']);

    Route::apiResource('experiences', ExperienceManagementController::class)
        ->only(['index', 'show', 'update', 'destroy']);

    // Reportes y estadísticas
    Route::get('reports/sales', [ReportController::class, 'salesBy']);
    Route::get('reports/usage', [ReportController::class, 'usageMetrics']);

    // Configuración
    Route::get('config', [SuperadminConfigController::class, 'show']);
    Route::put('config', [SuperadminConfigController::class, 'update']);

    // Seguridad
    Route::get('security/logs', [SecurityController::class, 'logs']);
    Route::get('security/audit', [SecurityController::class, 'auditTrail']);

    // Otros métodos específicos superadmin
    Route::post('/crear-usuario-emprendedor', [SuperadminController::class, 'crearUsuarioEmprendedor']);
    Route::get('/empresas/lista', [SuperadminController::class, 'listarEmpresas']);
    Route::get('/empresas/pendientes', [SuperadminController::class, 'listarEmpresasPendientes']);

    // Portales y diseño de portales
    Route::post('/portal', [PortalController::class, 'store']);
    Route::get('/portales', [PortalController::class, 'index']);
    Route::get('/portal/{id}/diseño', [PortalDesignController::class, 'show']);
    Route::post('/portal/diseño', [PortalDesignController::class, 'save']);
    Route::put('/portal/diseño/{id}', [PortalDesignController::class, 'update']);
    Route::delete('/portal/diseño/{id}', [PortalDesignController::class, 'destroy']);
});

// Rutas protegidas para EMPRENDEDOR
Route::middleware(['auth:sanctum', 'role:emprendedor'])->prefix('emprendedor')->group(function () {
    Route::post('/crear-empresa', [EmprendedorController::class, 'crearEmpresa']);
    Route::get('/estado-empresa', [EmprendedorController::class, 'estadoEmpresa']);

    // Gestión comercial
    Route::get('dashboard', [EmprendedorDashboardController::class, 'overview']);
    Route::apiResource('experiencias', ExperienceController::class);
    Route::apiResource('paquetes', PackageController::class);

    // Gestión de servicios
    Route::get('/servicios', [ServicioController::class, 'index']);
    Route::post('/servicios', [ServicioController::class, 'store']);
    Route::get('/servicios/{id}', [ServicioController::class, 'show']);
    Route::put('/servicios/{id}', [ServicioController::class, 'update']);

    // Reservas y atención
    Route::apiResource('reservas', ReservationController::class);
    Route::get('calendar/{serviceId}', [CalendarController::class, 'occupiedDates']);

    // Mensajes y comentarios
    Route::apiResource('mensajes', MessageController::class);

    // Promociones
    Route::apiResource('promociones', PromotionController::class);

    // Blog / Contenido
    Route::apiResource('blog', BlogController::class);

    // Perfil y configuración
    Route::get('perfil', [ProfileController::class, 'show']);
    Route::put('perfil', [ProfileController::class, 'update']);
    Route::get('config', [EmprendedorConfigController::class, 'show']);
    Route::put('config', [EmprendedorConfigController::class, 'update']);

    // Gestión de reservaciones específicas
    Route::get('/reservaciones', [ReservacionesController::class, 'index']);
    Route::get('/reservaciones/{id}', [ReservacionesController::class, 'show']);
    Route::put('/reservaciones/{id}/estado', [ReservacionesController::class, 'updateStatus']);
});

// Rutas protegidas para TURISTA
Route::middleware(['auth:sanctum', 'role:turista'])->prefix('turista')->group(function () {
    Route::post('/reservas', [ReservaPublicaController::class, 'store']);
    Route::get('/reservas', [ReservaPublicaController::class, 'index']);
});
