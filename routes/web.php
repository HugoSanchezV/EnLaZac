<?php

use App\Http\Controllers\DevicesController;
use App\Http\Controllers\RouterController;
use App\Http\Controllers\PingController;
use App\Http\Controllers\RouterosApiController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\ContractController;
use App\Http\Controllers\DeviceHistoriesController;
use App\Http\Controllers\InventorieDevicesController;
use App\Http\Controllers\PlanController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PayController;
use App\Http\Controllers\PingDeviceHistorieController;
use App\Http\Controllers\ScheduledTaskController;
use App\Http\Controllers\StatisticsController;
use App\Models\PingDeviceHistorie;
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

<<<<<<< Updated upstream
    // Route::get('/dashboard', [StatisticsController::class, 'show'])->name('dashboard');

    Route::middleware(['rol:1,2,3'])->group(function () {
=======
   // Route::get('/dashboard', [StatisticsController::class, 'show'])->name('dashboard');
   Route::get('/dashboard', [StatisticsController::class, 'show'])->name('dashboard');
   //MIDLEWARE ADMINISTRADOR
    Route::middleware(['rol:1'])->group(function () {
>>>>>>> Stashed changes
        // Usuarios
        Route::get('/usuarios',                 [UserController::class, 'index'])->name('usuarios');
        Route::get('/usuarios/show/{user}',     [UserController::class, 'show'])->name('usuarios.show');
        Route::get('/usuarios/create',          [UserController::class, 'create'])->name('usuarios.create');
        Route::post('/usuarios/store',          [UserController::class, 'store'])->name('usuarios.store');
        Route::get('/usuarios/edit/{id}',       [UserController::class, 'edit'])->name('usuarios.edit');
        Route::put('/usuarios/update/{id}',     [UserController::class, 'update'])->name('usuarios.update');
        Route::delete('/usuarios/delete/{id}',  [UserController::class, 'destroy'])->name('usuarios.destroy');
        Route::get('/usuarios/to/excel',  [UserController::class, 'exportExcel'])->name('usuarios.excel');


        //Routers
        // -- Resource 
        Route::get('/routers',                  [RouterController::class, 'index'])->name('routers');
        Route::get('/routers/create',           [RouterController::class, 'create'])->name('routers.create');
        Route::post('/routers/store',           [RouterController::class, 'store'])->name('routers.store');
        Route::get('/routers/edit/{id}',        [RouterController::class, 'edit'])->name('routers.edit');
        Route::put('/routers/update/{id}',      [RouterController::class, 'update'])->name('routers.update');
        Route::delete('/routers/delete/{id}',   [RouterController::class, 'destroy'])->name('routers.destroy');
        Route::get('/routers/to/excel',  [RouterController::class, 'exportExcel'])->name('routers.excel');

        // -- sync
        Route::get('/routers/{id}/sync',        [RouterController::class, 'sync'])->name('routers.sync');
        // -- devices
        Route::get('/routers/{router}/devices',     [RouterController::class, 'devices'])->name('routers.devices');
        Route::get('/routers/{router}/devices/to/excel',     [RouterController::class, 'devicesExportExcel'])->name('routers.devices.excel');
        //
        Route::get('/routers/ping/{id}',     [RouterController::class, 'sendPing'])->name('routers.ping');
        //-- Automatización del ping para routers
        Route::put('/routers/scheduled/ping/{id}',     [ScheduledTaskController::class, 'toggleTask'])->name('routers.scheduled.ping');
        //-- Historial de los pings
        Route::get('/pings',                  [PingController::class, 'index'])->name('pings');
        Route::delete('/pings/delete/{device}',          [PingController::class, 'destroy'])->name('pings.destroy');

    

        // Devices
        Route::get('/devices',                  [DevicesController::class, 'index'])->name('devices');
        Route::get('/devices/show',                  [DevicesController::class, 'show'])->name('devices.show');
        Route::get('/devices/{router}/create',                  [DevicesController::class, 'create'])->name('devices.create');
        Route::post('/devices/store',                           [DevicesController::class, 'store'])->name('devices.store');
        Route::get('/devices/{router}/edit/{device}',          [DevicesController::class, 'edit'])->name('devices.edit');
        Route::get('/devices/{router}/all/edit/{device}',          [DevicesController::class, 'device_all_edit'])->name('devices.all.edit');
        Route::put('/devices/update/{device}',          [DevicesController::class, 'update'])->name('devices.update');
        Route::put('/devices/all/update/{device}',          [DevicesController::class, 'device_all_update'])->name('devices.all.update');
        Route::delete('/devices/delete/{device}',          [DevicesController::class, 'destroy'])->name('devices.destroy');
        Route::delete('/devices/all/delete/{device}',          [DevicesController::class, 'device_all_destroy'])->name('devices.all.destroy');
        // -- device red
        Route::patch('/devices/set/device/status/{device}',     [DevicesController::class, 'setDeviceStatus'])->name('devices.set.status');
        Route::patch('/devices/all/set/device/status/{device}',     [DevicesController::class, 'AllsetDeviceStatus'])->name('devices.all.set.status');
        Route::get('/devices/set/device/ping/{device}',  [DevicesController::class, 'sendPing'])->name('devices.ping');
        // -- ping
        Route::get('/devices/{router}/ping',                  [DevicesController::class, 'pingAllDevice'])->name('devices.all.ping');
        //Route::get('/devices/{router}/show/ping/status',       [DevicesController::class, 'showPingDevice'])->name('devices.show.all.ping');
        //Route::get('/devices/all/set/device/ping/{device}',  [DevicesController::class, 'sendAllPing'])->name('devices.all.ping');
        Route::get('/devices/all/to/excel',     [DevicesController::class, 'allDevicesExportExcel'])->name('devices.all.excel');
        //Ping Devices Historie
        Route::get('/devices/ping/historie',     [PingDeviceHistorieController::class, 'index'])->name('device.ping.historie');



        // inventorie_devices
        Route::get('/inventorie/devices',                 [InventorieDevicesController::class, 'index'])->name('inventorie.devices.index');
        Route::get('/inventorie/devices/show/{inventorieDevice}', [InventorieDevicesController::class, 'show'])->name('inventorie.devices.show');
        Route::get('/inventorie/devices/create',          [InventorieDevicesController::class, 'create'])->name('inventorie.devices.create');
        Route::post('/inventorie/devices/store',          [InventorieDevicesController::class, 'store'])->name('inventorie.devices.store');
        Route::get('/inventorie/devices/edit/{device}',          [InventorieDevicesController::class, 'edit'])->name('inventorie.devices.edit');
        Route::put('/inventorie/devices/update/{device}',          [InventorieDevicesController::class, 'update'])->name('inventorie.devices.update');
        Route::delete('/inventorie/devices/delete/{device}',          [InventorieDevicesController::class, 'destroy'])->name('inventorie.devices.destroy');
        Route::get('/inventorie/devices/to/excel',          [InventorieDevicesController::class, 'exportExcel'])->name('inventorie.devices.excel');

        Route::get('/inventorie/devices/histories',          [DeviceHistoriesController::class, 'index'])->name('historieDevices.index');
        Route::delete('/inventorie/devices/histories/{device}/delete',          [DeviceHistoriesController::class, 'destroy'])->name('historieDevices.destroy');
        Route::get('/inventorie/devices/histories/to/excel',          [DeviceHistoriesController::class, 'exportExcel'])->name('historieDevices.excel');

        //Tickets coordi
        Route::get('/tickets',                   [TicketController::class, 'index'])->name('tickets');
        Route::post('/tickets/statusUpdate/{id}',[TicketController::class, 'statusUpdate'])->name('tickets.statusUpdate');
        Route::get('/tickets/create',            [TicketController::class, 'create'])->name('tickets.create');
        Route::get('/tickets/show/{id}',         [TicketController::class, 'show'])->name('tickets.show');
        Route::get('/tickets/create',            [TicketController::class, 'create'])->name('tickets.create');
        Route::post('/tickets/store',            [TicketController::class, 'store'])->name('tickets.store');
        Route::get('/tickets/edit/{id}',         [TicketController::class, 'edit'])->name('tickets.edit');
        Route::put('/tickets/update/{id}',       [TicketController::class, 'update'])->name('tickets.update');
        Route::delete('/tickets/delete/{id}',    [TicketController::class, 'destroy'])->name('tickets.destroy');
        //Leer y marcado como leída las notificaciones
       

        //Contracts Coordi
        Route::get('/contracts',                 [ContractController::class, 'index'])->name('contracts');
        Route::get('/contracts/create',          [ContractController::class, 'create'])->name('contracts.create');
        Route::get('/contracts/show/{id}',       [ContractController::class, 'show'])->name('contracts.show');
        Route::post('/contracts/store',          [ContractController::class, 'store'])->name('contracts.store');
        Route::get('/contracts/edit/{id}',       [ContractController::class, 'edit'])->name('contracts.edit');
        Route::put('/contracts/update/{id}',     [ContractController::class, 'update'])->name('contracts.update');
        Route::delete('/contracts/delete/{id}',  [ContractController::class, 'destroy'])->name('contracts.destroy');
        Route::get('/contracts/to/excel',  [ContractController::class, 'exportExcel'])->name('contracts.excel');


        //Planes de internet
        Route::get('/plans',                     [PlanController::class, 'index'])->name('plans');
        Route::get('/plans/create',              [PlanController::class, 'create'])->name('plans.create');
        Route::get('/plans/show/{id}',           [PlanController::class, 'show'])->name('plans.show');
        Route::post('/plans/store',              [PlanController::class, 'store'])->name('plans.store');
        Route::get('/plans/edit/{id}',           [PlanController::class, 'edit'])->name('plans.edit');
        Route::put('/plans/update/{id}',         [PlanController::class, 'update'])->name('plans.update');
        Route::delete('/plans/delete/{id}',      [PlanController::class, 'destroy'])->name('plans.destroy');
<<<<<<< Updated upstream
=======
        
    
        Route::post('/notifications/read/{id}',  [NotificationController::class, 'markAsRead']);
        Route::get('/notifications/unread',      [NotificationController::class, 'unread']);
>>>>>>> Stashed changes
    });

    //MIDDLEWARE DEMÁS USUARIOS
    Route::middleware(['rol:2,3'])->group(function () {
        // Usuarios
      /*  Route::get('/dashboard', function () {
            return Inertia::render('DashboardBase');
        })->name('dashboard');*/
       // Route::get('/dashboard', [StatisticsController::class, 'show'])->name('dashboard');

    });


    //Vista generales
  /*  Route::get('/tickets/create',            [TicketController::class, 'create'])->name('tickets.create');
    Route::get('/tickets/show/{id}',         [TicketController::class, 'show'])->name('tickets.show');
    Route::get('/tickets/create',            [TicketController::class, 'create'])->name('tickets.create');
    Route::post('/tickets/store',            [TicketController::class, 'store'])->name('tickets.store');
    Route::get('/tickets/edit/{id}',         [TicketController::class, 'edit'])->name('tickets.edit');
    Route::put('/tickets/update/{id}',       [TicketController::class, 'update'])->name('tickets.update');
    Route::delete('/tickets/delete/{id}',    [TicketController::class, 'destroy'])->name('tickets.destroy');*/
    
    //MIDDLEWARE DEL CLIENTE
    Route::middleware(['rol:0'])->group(function () {
        Route::get('/tickets/usuario',                 [TicketController::class, 'index2'])->name('tickets.usuario');
        Route::post('/tickets/store/usuario',          [TicketController::class, 'store'])->name('tickets.usuario.store');

<<<<<<< Updated upstream
    //Vistas del usuario
    Route::middleware(['rol:0'])->group(function () {
        /*   Route::get('/dashboard', function () {
            return Inertia::render('DashboardBase');
        })->name('dashboard');
     */
        // Route::get('/dashboard', [StatisticsController::class, 'show'])->name('dashboard');

        Route::get('/tickets/usuario',                 [TicketController::class, 'index_user'])->name('tickets.usuario');
        Route::post('/tickets/store/usuario',          [TicketController::class, 'store'])->name('tickets.store.usuario');
        Route::get('/pagos',                     [PayController::class, 'index'])->name('pays');
    });
=======
    });


    Route::get('/pagos',                     [PayController::class, 'index'])->name('pays');
>>>>>>> Stashed changes
});
