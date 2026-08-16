<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

// ==========================================
// IMPORTACIONES DE CONTROLADORES Y MODELOS
// ==========================================
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TicketController; 
use App\Http\Controllers\RatingController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\DashboardController;

use App\Models\User;
use App\Models\Inventory;
use App\Models\Ticket;

// ==========================================
// RUTAS PÚBLICAS Y DE AUTENTICACIÓN
// ==========================================
Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');


// ==========================================
// RUTAS PROTEGIDAS (Requieren inicio de sesión)
// ==========================================
Route::middleware('auth')->group(function () {

    // ------------------------------------------
    // DASHBOARD PRINCIPAL
    // ------------------------------------------
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Endpoint JSON que alimenta las gráficas en tiempo real (polling)
    Route::get('/dashboard/stats', [DashboardController::class, 'stats'])->name('dashboard.stats');

    // ------------------------------------------
    // PERFIL DE USUARIO
    // ------------------------------------------
    Route::get('/perfil', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/perfil', [ProfileController::class, 'update'])->name('profile.update');
    Route::get('/cambiar-contrasena', [ProfileController::class, 'passwordEdit'])->name('password.edit');
    Route::put('/cambiar-contrasena', [ProfileController::class, 'passwordUpdate'])->name('password.update');

    // ------------------------------------------
    // TICKETS Y CALIFICACIONES (Usuarios)
    // ------------------------------------------
    Route::get('/tickets/crear', [TicketController::class, 'create'])->name('tickets.create');
    Route::post('/tickets', [TicketController::class, 'store'])->name('tickets.store');

    // Descarga del adjunto de un ticket. Va por una ruta autenticada
    // (y no por /storage público) para que el archivo solo lo pueda ver
    // el dueño del ticket o el personal de soporte.
    Route::get('/tickets/{id}/adjunto', [TicketController::class, 'descargarAdjunto'])->name('tickets.adjunto');

    Route::get('/tickets/{id}/calificar', [RatingController::class, 'create'])->name('ratings.create');
    Route::post('/tickets/{id}/calificar', [RatingController::class, 'store'])->name('ratings.store');

    // ------------------------------------------
    // PANEL DE SOPORTE (Técnicos)
    // ------------------------------------------
    Route::middleware('role:soporte,admin')->group(function () {
        Route::get('/soporte/tickets', [TicketController::class, 'index'])->name('soporte.index');
        Route::get('/soporte/tickets/{id}', [TicketController::class, 'show'])->name('soporte.show');
        Route::put('/soporte/tickets/{id}', [TicketController::class, 'update'])->name('soporte.update');
    });

    // ------------------------------------------
    // INVENTARIO GENERAL (Soporte/Admin)
    // ------------------------------------------
    // Todo el módulo queda detrás del middleware de rol. Antes estas
    // rutas solo pedían estar autenticado, así que cualquier usuario
    // podía listar e incluso eliminar equipos llamando la URL directa.
    Route::middleware('role:soporte,admin')->group(function () {
        Route::get('/inventario/pdf', [InventoryController::class, 'generatePDF'])->name('inventory.downloadPdf');

        Route::get('/inventario/asignar', [InventoryController::class, 'asignar'])->name('inventory.assign');
        Route::post('/inventario/asignar', [InventoryController::class, 'storeAssign'])->name('inventory.storeAssign');
        Route::put('/inventario/{id}/devolver', [InventoryController::class, 'devolver'])->name('inventory.return');

        Route::resource('inventory', InventoryController::class);

        Route::get('/inventario/crear', [InventoryController::class, 'create'])->name('inventario.create');
        Route::post('/inventario', [InventoryController::class, 'store'])->name('inventario.store');
    });

    // ------------------------------------------
    // GESTIÓN DE USUARIOS (Solo Admin)
    // ------------------------------------------
    Route::middleware('role:admin')->group(function () {
        Route::get('/usuarios', [UserController::class, 'index'])->name('users.index');
        Route::get('/usuarios/crear', [UserController::class, 'create'])->name('users.create');
        Route::post('/usuarios', [UserController::class, 'store'])->name('users.store');
        Route::get('/usuarios/{id}/editar', [UserController::class, 'edit'])->name('users.edit');
        Route::put('/usuarios/{id}', [UserController::class, 'update'])->name('users.update');
    });

});