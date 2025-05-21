<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
// routes/api.php

use Laravel\Sanctum\Http\Controllers\CsrfCookieController;

Route::get('/sanctum/csrf-cookie', [CsrfCookieController::class, 'show']);


// --- AUTH PÚBLICO ---
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\Auth\GoogleController;

// --- CONTROLADORES PÚBLICOS ---
use App\Http\Controllers\Publico\PublicServiceController;
use App\Http\Controllers\Publico\PublicCategoryController;
use App\Http\Controllers\Publico\PublicPackageController;
use App\Http\Controllers\Publico\CartController;
use App\Http\Controllers\Publico\CheckoutController;
use App\Http\Controllers\Publico\CalendarController as PublicCalendarController;

// --- CONTROLADORES AUXILIARES ---
use App\Http\Controllers\LocationController;
use App\Http\Controllers\CategoryController;

// --- CONTROLADORES SUPERADMIN ---
use App\Http\Controllers\Superadmin\DashboardController        as SuperadminDashboardController;
use App\Http\Controllers\Superadmin\UserManagementController;
use App\Http\Controllers\Superadmin\CompanyManagementController;
use App\Http\Controllers\Superadmin\ContentManagementController;
use App\Http\Controllers\Superadmin\ExperienceManagementController;
use App\Http\Controllers\Superadmin\ReportController;
use App\Http\Controllers\Superadmin\ConfigController           as SuperadminConfigController;
use App\Http\Controllers\Superadmin\SecurityController;
use App\Http\Controllers\Superadmin\SuperadminController;
use App\Http\Controllers\Superadmin\PortalController;
use App\Http\Controllers\Superadmin\PortalDesignController;

// --- CONTROLADORES EMPRENDEDOR ---
use App\Http\Controllers\Emprendedor\DashboardController      as EmprendedorDashboardController;
use App\Http\Controllers\Emprendedor\PackageController        as EmprendedorPackageController;
use App\Http\Controllers\Emprendedor\ReservationController    as EmprendedorReservationController;
use App\Http\Controllers\Emprendedor\MessageController;
use App\Http\Controllers\Emprendedor\PromotionController;
use App\Http\Controllers\Emprendedor\BlogController;
use App\Http\Controllers\Emprendedor\ProfileController         as EmprendedorProfileController;
use App\Http\Controllers\Emprendedor\ConfigController          as EmprendedorConfigController;
use App\Http\Controllers\Emprendedor\ReservacionesController;
use App\Http\Controllers\Emprendedor\ServicioController;
use App\Http\Controllers\Emprendedor\CalendarController        as EmprendedorCalendarController;

// --- CONTROLADORES TURISTA ---
use App\Http\Controllers\Turista\DashboardController          as TuristaDashboardController;
use App\Http\Controllers\Turista\ExperienceController         as TuristaExperienceController;
use App\Http\Controllers\Turista\PackageController            as TuristaPackageController;
use App\Http\Controllers\Turista\ReservationController        as TuristaReservationController;
use App\Http\Controllers\Turista\FavoriteController;
use App\Http\Controllers\Turista\ReviewController;
use App\Http\Controllers\Turista\ProfileController             as TuristaProfileController;
use App\Http\Controllers\Turista\PaymentMethodController;
use App\Http\Controllers\Turista\ConfigController              as TuristaConfigController;
use App\Http\Controllers\Turista\ReservaPublicaController;

// --- RUTAS DE AUTENTICACIÓN PÚBLICA ---
Route::prefix('auth')->group(function () {
    Route::post('register-turista', [AuthController::class, 'registerTurista']);
    Route::post('login',            [AuthController::class, 'login']);
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('logout', [AuthController::class, 'logout']);
        Route::get('user',    [AuthController::class, 'user']);
    });
    Route::post('forgot-password', [PasswordResetController::class, 'sendResetLinkEmail']);
    Route::post('reset-password',  [PasswordResetController::class, 'reset']);
    Route::get('google',           [GoogleController::class, 'redirectToGoogle']);
    Route::get('google/callback',  [GoogleController::class, 'handleGoogleCallback']);
});

// --- RUTAS PÚBLICAS (SIN AUTH) ---
// Servicios, categorías y paquetes
Route::get('servicios-publicos',      [PublicServiceController::class, 'index']);
Route::get('servicios-publicos/{id}', [PublicServiceController::class, 'show']);
Route::get('categorias-publicas',     [PublicCategoryController::class, 'index']);
Route::get('paquetes-publicos',       [PublicPackageController::class, 'index']);

// Calendario público de ocupación
Route::get(
    'servicios-publicos/{service}/calendar',
    [PublicCalendarController::class, 'occupiedDates']
);

// Carrito (session-based, necesita middleware 'web' para que arranque la sesión)
Route::middleware('web')->group(function () {
    Route::get(   'cart',           [CartController::class, 'index']);
    Route::get(   'cart/summary',   [CartController::class, 'summary']);
    Route::post(  'cart/{service}', [CartController::class, 'add']);
    Route::delete('cart/{service}', [CartController::class, 'remove']);
});

// Selectores de UI
Route::get('locations',  [LocationController::class, 'index']);
Route::get('categories', [CategoryController::class, 'index']);

// --- RUTAS PROTEGIDAS ---

// Superadmin
Route::middleware(['auth:sanctum','role:superadmin'])
    ->prefix('superadmin')
    ->group(function () {
        Route::get('dashboard', [SuperadminDashboardController::class, 'overview']);
        Route::apiResource('users',       UserManagementController::class);
        Route::get('companies/pending',   [CompanyManagementController::class, 'pending']);
        Route::put('companies/{id}/approve', [CompanyManagementController::class, 'approve']);
        Route::put('companies/{id}/reject',  [CompanyManagementController::class, 'reject']);
        Route::apiResource('contents',     ContentManagementController::class)
             ->only(['index','show','update','destroy']);
        Route::apiResource('experiences',  ExperienceManagementController::class)
             ->only(['index','show','update','destroy']);
        Route::get('reports/sales',        [ReportController::class, 'salesBy']);
        Route::get('reports/usage',        [ReportController::class, 'usageMetrics']);
        Route::get('config',               [SuperadminConfigController::class, 'show']);
        Route::put('config',               [SuperadminConfigController::class, 'update']);
        Route::get('security/logs',        [SecurityController::class, 'logs']);
        Route::get('security/audit',       [SecurityController::class, 'auditTrail']);
        Route::post('crear-usuario-emprendedor', [SuperadminController::class, 'crearUsuarioEmprendedor']);
        Route::get('empresas/lista',             [SuperadminController::class, 'listarEmpresas']);
        Route::get('empresas/pendientes',        [SuperadminController::class, 'listarEmpresasPendientes']);
        Route::post('portal',                    [PortalController::class, 'store']);
        Route::get('portales',                   [PortalController::class, 'index']);
        Route::get('portal/{id}/diseño',         [PortalDesignController::class, 'show']);
        Route::post('portal/diseño',             [PortalDesignController::class, 'save']);
        Route::put('portal/diseño/{id}',         [PortalDesignController::class, 'update']);
        Route::delete('portal/diseño/{id}',      [PortalDesignController::class, 'destroy']);
    });

// Emprendedor
Route::middleware(['auth:sanctum','role:emprendedor'])
    ->prefix('emprendedor')
    ->group(function () {
        Route::post('crear-empresa', [EmprendedorController::class, 'crearEmpresa']);
        Route::get('estado-empresa', [EmprendedorController::class, 'estadoEmpresa']);
        Route::get('dashboard',      [EmprendedorDashboardController::class, 'overview']);

        // Servicios
        Route::get(   'servicios',                [ServicioController::class, 'index']);
        Route::post(  'servicios',                [ServicioController::class, 'store']);
        Route::get(   'servicios/{id}',           [ServicioController::class, 'show']);
        Route::put(   'servicios/{id}',           [ServicioController::class, 'update']);
        Route::delete('servicios/{id}',           [ServicioController::class, 'destroy']);
        Route::patch( 'servicios/{id}/toggle-active', [ServicioController::class, 'toggleActive']);

        // Paquetes y promociones
        Route::apiResource('paquetes',    EmprendedorPackageController::class);
        Route::apiResource('promociones', PromotionController::class);

        // Reservaciones internas
        Route::apiResource('reservas',    EmprendedorReservationController::class);
        Route::get('calendar/{service}',  [EmprendedorCalendarController::class, 'occupiedDates']);

        // Mensajes, blog, perfil y config
        Route::apiResource('mensajes', MessageController::class);
        Route::apiResource('blog',     BlogController::class);
        Route::get('perfil', [EmprendedorProfileController::class, 'show']);
        Route::put('perfil', [EmprendedorProfileController::class, 'update']);
        Route::get('config', [EmprendedorConfigController::class, 'show']);
        Route::put('config', [EmprendedorConfigController::class, 'update']);

        // Reservaciones para emprendedor
        Route::get('reservaciones',            [ReservacionesController::class, 'index']);
        Route::get('reservaciones/{id}',       [ReservacionesController::class, 'show']);
        Route::put('reservaciones/{id}/estado',[ReservacionesController::class, 'updateStatus']);
    });

// Turista
Route::middleware(['auth:sanctum','role:turista'])
    ->prefix('turista')
    ->group(function () {
        // Reservas públicas (pre-reserva)
        Route::post('reservas', [ReservaPublicaController::class, 'store']);
        Route::get( 'reservas', [ReservaPublicaController::class, 'index']);

        // Dashboard, exploración
        Route::get('dashboard',    [TuristaDashboardController::class, 'overview']);
        Route::get('experiencias', [TuristaExperienceController::class, 'index']);
        Route::get('paquetes',     [TuristaPackageController::class, 'index']);

        // Gestión de reservas privadas
        Route::apiResource('reservas', TuristaReservationController::class)
             ->only(['show','update','destroy']);
        Route::apiResource('favoritos', FavoriteController::class);
        Route::apiResource('reseñas',   ReviewController::class);

        // Perfil y métodos de pago
        Route::get('perfil', [TuristaProfileController::class, 'show']);
        Route::put('perfil', [TuristaProfileController::class, 'update']);
        Route::apiResource('metodos-pago', PaymentMethodController::class);
        Route::get('config', [TuristaConfigController::class, 'show']);
        Route::put('config', [TuristaConfigController::class, 'update']);

        // Checkout / Pre-reserva (sesión + auth)
        Route::post('checkout', [CheckoutController::class, 'checkout']);

         // Marcar como pagada
        Route::post('reservas/{id}/pago', [App\Http\Controllers\Turista\ReservationController::class, 'pay']);


    });
