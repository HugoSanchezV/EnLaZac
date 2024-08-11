<?php

use App\Http\Controllers\DevicesController;
use App\Http\Controllers\RouterController;
use App\Http\Controllers\RouterosApiController;
use App\Http\Controllers\UserController;
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

    Route::patch('/devices/set/device/status/{device}',     [DevicesController::class, 'setDeviceStatus'])->name('devices.set.status');

});

Route::get('/test/api', [RouterosApiController::class, 'index'])->name('test.index');
