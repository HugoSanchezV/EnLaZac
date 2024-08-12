<?php

use App\Http\Controllers\RouterController;
use App\Http\Controllers\RouterosApiController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\TicketController;

use App\Models\Ticket;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {

    Route::get('/dashboard', function () {
        return Inertia::render('DashboardBase');
    })->name('dashboard');

    // Usuarios
    Route::get('/usuarios',                 [UserController::class, 'index'])->name('usuarios');
    Route::get('/usuarios/create',          [UserController::class, 'create'])->name('usuarios.create');
    Route::post('/usuarios/store',          [UserController::class, 'store'])->name('usuarios.store');
    Route::get('/usuarios/edit/{id}',       [UserController::class, 'edit'])->name('usuarios.edit');
    Route::put('/usuarios/update/{id}',     [UserController::class, 'update'])->name('usuarios.update');
    Route::delete('/usuarios/delete/{id}',  [UserController::class, 'destroy'])->name('usuarios.destroy');

    //Routers
        // Resource 
    Route::get('/routers',                  [RouterController::class, 'index'])->name('routers');
    Route::get('/routers/create',           [RouterController::class, 'create'])->name('routers.create');
    Route::post('/routers/store',           [RouterController::class, 'store'])->name('routers.store');
    Route::get('/routers/edit/{id}',        [RouterController::class, 'edit'])->name('routers.edit');
    Route::put('/routers/update/{id}',      [RouterController::class, 'update'])->name('routers.update');
    Route::delete('/routers/delete/{id}',   [RouterController::class, 'destroy'])->name('routers.destroy');
        // sync
    Route::get('/routers/{id}/sync',        [RouterController::class, 'sync'])->name('routers.sync');
        // devices
    Route::get('/routers/{router}/devices',     [RouterController::class, 'devices'])->name('routers.devices');
    Route::get('/devices/create',     [RouterController::class, 'devices'])->name('devices.create');

    //Tickets
    Route::get('/tickets',                 [TicketController::class, 'index'])->name('tickets');
    Route::get('/tickets/create',          [TicketController::class, 'create'])->name('tickets.create');
    Route::post('/tickets/store',          [TicketController::class, 'store'])->name('tickets.store');
    Route::get('/tickets/edit/{id}',       [TicketController::class, 'edit'])->name('tickets.edit');
    Route::put('/tickets/update/{id}',     [TicketController::class, 'update'])->name('tickets.update');
    Route::delete('/tickets/delete/{id}',  [TicketController::class, 'destroy'])->name('tickets.destroy');
});

Route::get('/test/api', [RouterosApiController::class, 'index'])->name('test.index');
