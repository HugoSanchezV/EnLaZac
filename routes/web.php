<?php

use App\Http\Controllers\DevicesController;
use App\Http\Controllers\RouterController;
use App\Http\Controllers\RouterosApiController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\ContractController;
use App\Http\Controllers\InventorieDevicesController;
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
    // -- Resource 
    Route::get('/routers',                  [RouterController::class, 'index'])->name('routers');
    Route::get('/routers/create',           [RouterController::class, 'create'])->name('routers.create');
    Route::post('/routers/store',           [RouterController::class, 'store'])->name('routers.store');
    Route::get('/routers/edit/{id}',        [RouterController::class, 'edit'])->name('routers.edit');
    Route::put('/routers/update/{id}',      [RouterController::class, 'update'])->name('routers.update');
    Route::delete('/routers/delete/{id}',   [RouterController::class, 'destroy'])->name('routers.destroy');
    // -- sync
    Route::get('/routers/{id}/sync',        [RouterController::class, 'sync'])->name('routers.sync');
    // -- devices
    Route::get('/routers/{router}/devices',     [RouterController::class, 'devices'])->name('routers.devices');

    // Devices
    Route::get('/devices/{router}/create',                  [DevicesController::class, 'create'])->name('devices.create');
    Route::post('/devices/store',                           [DevicesController::class, 'store'])->name('devices.store');
    Route::get('/devices/{router}/edit/{device}',          [DevicesController::class, 'edit'])->name('devices.edit');
    Route::put('/devices/update/{device}',          [DevicesController::class, 'update'])->name('devices.update');
    Route::delete('/devices/delete/{device}',          [DevicesController::class, 'destroy'])->name('devices.destroy');
    // -- status 
    Route::patch('/devices/set/device/status/{device}',     [DevicesController::class, 'setDeviceStatus'])->name('devices.set.status');

    // inventorie_devices
    Route::get('/inventorie/devices',                 [InventorieDevicesController::class, 'index'])->name('inventorie.devices.index');
    Route::get('/inventorie/devices/create',          [InventorieDevicesController::class, 'create'])->name('inventorie.devices.create');
    Route::post('/inventorie/devices/store',          [InventorieDevicesController::class, 'store'])->name('inventorie.devices.store');
    Route::get('/inventorie/devices/edit/{device}',          [InventorieDevicesController::class, 'edit'])->name('inventorie.devices.edit');
    Route::put('/inventorie/devices/update/{device}',          [InventorieDevicesController::class, 'update'])->name('inventorie.devices.update');
    Route::delete('/inventorie/devices/delete/{device}',          [InventorieDevicesController::class, 'destroy'])->name('inventorie.devices.destroy');
    //Tickets coordi
    Route::get('/tickets',                 [TicketController::class, 'index'])->name('tickets');
    Route::get('/tickets/create',          [TicketController::class, 'create'])->name('tickets.create');
    Route::get('/tickets/show/{id}',          [TicketController::class, 'show'])->name('tickets.show');
    Route::post('/tickets/store',          [TicketController::class, 'store'])->name('tickets.store');
    Route::get('/tickets/edit/{id}',       [TicketController::class, 'edit'])->name('tickets.edit');
    Route::put('/tickets/update/{id}',     [TicketController::class, 'update'])->name('tickets.update');
    Route::delete('/tickets/delete/{id}',  [TicketController::class, 'destroy'])->name('tickets.destroy');
    Route::post('/tickets/statusUpdate/{id}', [TicketController::class, 'statusUpdate'])->name('tickets.statusUpdate');

    //Tickets user
    //Route::get('/tickets/usuario',                 [TicketController::class, 'index2'])->name('tickets');
    //Route::post('/tickets/store/usuario',          [TicketController::class, 'store'])->name('tickets.store');

    //Contracts Coordi
    Route::get('/contracts',                 [ContractController::class, 'index'])->name('contracts');
    Route::get('/contracts/create',          [ContractController::class, 'create'])->name('contracts.create');
    Route::get('/contracts/show/{id}',          [ContractController::class, 'show'])->name('contracts.show');
    Route::post('/contracts/store',          [ContractController::class, 'store'])->name('contracts.store');
    Route::get('/contracts/edit/{id}',       [ContractController::class, 'edit'])->name('contracts.edit');
    Route::put('/contracts/update/{id}',     [ContractController::class, 'update'])->name('contracts.update');
    Route::delete('/contracts/delete/{id}',  [ContractController::class, 'destroy'])->name('contracts.destroy');
});

Route::get('/test/api', [RouterosApiController::class, 'index'])->name('test.index');
