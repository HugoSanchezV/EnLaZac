<?php

use App\Http\Controllers\ChargeController;
use App\Http\Controllers\BackupsController;
use App\Http\Controllers\DevicesController;
use App\Http\Controllers\RouterController;
use App\Http\Controllers\PingController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\ContractController;
use App\Http\Controllers\DeviceHistoriesController;
use App\Http\Controllers\InterestsController;
use App\Http\Controllers\InventorieDevicesController;
use App\Http\Controllers\PlanController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PayController;
use App\Http\Controllers\PayPalSettingController;
use App\Http\Controllers\PingDeviceHistorieController;
use App\Http\Controllers\PreRegisterUserController;
use App\Http\Controllers\RuralCommunityController;
use App\Http\Controllers\ScheduledTaskController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\StatisticsController;
use App\Http\Controllers\TechnicalDeviceHistoriesController;
use App\Http\Controllers\TechnicalDevicesController;
use App\Http\Controllers\TechnicalInventorieDevicesController;
use App\Http\Controllers\TechnicalRouterController;
use App\Http\Controllers\TechnicalTicketController;
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

    // Route::get('/dashboard', [StatisticsController::class, 'show'])->name('dashboard');
    Route::get('/dashboard', [StatisticsController::class, 'show'])->name('dashboard');
    //MIDLEWARE ADMINISTRADOR
    Route::middleware(['rol:1'])->group(function () {
        // Usuarios
        Route::get('/usuarios',                 [UserController::class, 'index'])->name('usuarios');
        Route::get('/usuarios/show/{user}',     [UserController::class, 'show'])->name('usuarios.show');
        Route::get('/usuarios/create',          [UserController::class, 'create'])->name('usuarios.create');
        Route::post('/usuarios/store',          [UserController::class, 'store'])->name('usuarios.store');
        Route::get('/usuarios/edit/{id}',       [UserController::class, 'edit'])->name('usuarios.edit');
        Route::put('/usuarios/update/{id}',     [UserController::class, 'update'])->name('usuarios.update');
        Route::delete('/usuarios/delete/{id}',  [UserController::class, 'destroy'])->name('usuarios.destroy');
        Route::get('/usuarios/to/excel',        [UserController::class, 'exportExcel'])->name('usuarios.excel');
        Route::post('/usuarios/import/excel',   [UserController::class, 'importExcel'])->name('usuarios.import.excel');

        Route::get('/usuarios/pre/register/',                     [PreRegisterUserController::class, 'index'])->name('usuarios.pre.register');
        Route::get('/usuarios/pre/register/create',               [PreRegisterUserController::class, 'create'])->name('usuarios.pre.register.create');
        Route::post('/usuarios/pre/register/store',                [PreRegisterUserController::class, 'store'])->name('usuarios.pre.register.store');
        Route::get('/usuarios/pre/register/edit/{PreRegisterUser}',            [PreRegisterUserController::class, 'edit'])->name('usuarios.pre.register.edit');
        Route::put('/usuarios/pre/register/update/{id}',          [PreRegisterUserController::class, 'update'])->name('usuarios.pre.register.update');
        Route::delete('/usuarios/pre/register/delete/{id}',          [PreRegisterUserController::class, 'destroy'])->name('usuarios.pre.register.destroy');

        //Routers
        // -- Resource 
        Route::get('/routers',                  [RouterController::class, 'index'])->name('routers');
        Route::get('/routers/show/{id}',        [RouterController::class, 'show'])->name('routers.show');
        Route::get('/routers/create',           [RouterController::class, 'create'])->name('routers.create');
        Route::post('/routers/store',           [RouterController::class, 'store'])->name('routers.store');
        Route::get('/routers/edit/{id}',        [RouterController::class, 'edit'])->name('routers.edit');
        Route::put('/routers/update/{id}',      [RouterController::class, 'update'])->name('routers.update');
        Route::delete('/routers/delete/{id}',   [RouterController::class, 'destroy'])->name('routers.destroy');
        Route::get('/routers/to/excel',         [RouterController::class, 'exportExcel'])->name('routers.excel');
        Route::post('/routers/import/excel',    [RouterController::class, 'importExcel'])->name('routers.import.excel');

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
        Route::get('/devices/show/{id}',                  [DevicesController::class, 'show'])->name('devices.show');
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
        Route::get('/devices/all/set/device/ping/{device}',  [DevicesController::class, 'sendAllPing'])->name('devices.one.ping');
        Route::get('/devices/all/to/excel',     [DevicesController::class, 'allDevicesExportExcel'])->name('devices.all.excel');
        Route::post('/devices/all/import/excel',     [DevicesController::class, 'allDevicesImportExcel2'])->name('devices.import.excel');
        // Route::post('/devices/all/to/local/import/excel',     [DevicesController::class, 'allDevicesToLocalImportExcel'])->name('devices.to.local.import.excel');
        //Ping Devices Historie
        Route::get('/devices/ping/historie',     [PingDeviceHistorieController::class, 'index'])->name('device.ping.historie');
        Route::get('/routers/{router}/devices/ping/historie',     [PingDeviceHistorieController::class, 'index2'])->name('router.device.ping.historie');
        Route::put('/devices/ping/historie/update/{id}',     [PingDeviceHistorieController::class, 'update'])->name('device.ping.historie.update');
        Route::delete('/devices/ping/historie/delete/{id}',     [PingDeviceHistorieController::class, 'destroy'])->name('device.ping.historie.destroy');

        // inventorie_devices
        Route::get('/inventorie/devices',                 [InventorieDevicesController::class, 'index'])->name('inventorie.devices.index');
        Route::get('/inventorie/devices/show/{id}', [InventorieDevicesController::class, 'show'])->name('inventorie.devices.show');
        Route::get('/inventorie/devices/create',          [InventorieDevicesController::class, 'create'])->name('inventorie.devices.create');
        Route::post('/inventorie/devices/store',          [InventorieDevicesController::class, 'store'])->name('inventorie.devices.store');
        Route::get('/inventorie/devices/edit/{device}',          [InventorieDevicesController::class, 'edit'])->name('inventorie.devices.edit');
        Route::put('/inventorie/devices/update/{device}',          [InventorieDevicesController::class, 'update'])->name('inventorie.devices.update');
        Route::delete('/inventorie/devices/delete/{device}',          [InventorieDevicesController::class, 'destroy'])->name('inventorie.devices.destroy');
        Route::get('/inventorie/devices/to/excel',          [InventorieDevicesController::class, 'exportExcel'])->name('inventorie.devices.excel');
        Route::post('/inventorie/devices/histories/import/excel',          [InventorieDevicesController::class, 'importExcel'])->name('historieDevices.import.excel');

        Route::get('/inventorie/devices/histories',          [DeviceHistoriesController::class, 'index'])->name('historieDevices.index');
        Route::get('/inventorie/devices/histories/show/{DeviceHistorie}',          [DeviceHistoriesController::class, 'index'])->name('historieDevices.show');
        Route::get('/inventorie/devices/histories/to/excel/{id}',          [DeviceHistoriesController::class, 'exportExcel'])->name('historieDevices.excel.historie');

        Route::delete('/inventorie/devices/histories/{device}/delete',          [DeviceHistoriesController::class, 'destroy'])->name('historieDevices.destroy');
        Route::get('/inventorie/devices/histories/to/excel',          [DeviceHistoriesController::class, 'exportExcel'])->name('historieDevices.excel');
        //Tickets coordi
        Route::get('/tickets',                   [TicketController::class, 'index'])->name('tickets');
        Route::post('/tickets/statusUpdate/{id}', [TicketController::class, 'statusUpdate'])->name('tickets.statusUpdate');
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
        Route::get('/contracts/to/excel',        [ContractController::class, 'exportExcel'])->name('contracts.excel');

        //Planes de internet
        Route::get('/plans',                     [PlanController::class, 'index'])->name('plans');
        Route::get('/plans/create',              [PlanController::class, 'create'])->name('plans.create');
        Route::get('/plans/show/{id}',           [PlanController::class, 'show'])->name('plans.show');
        Route::post('/plans/store',              [PlanController::class, 'store'])->name('plans.store');
        Route::get('/plans/edit/{id}',           [PlanController::class, 'edit'])->name('plans.edit');
        Route::put('/plans/update/{id}',         [PlanController::class, 'update'])->name('plans.update');
        Route::delete('/plans/delete/{id}',      [PlanController::class, 'destroy'])->name('plans.destroy');

        Route::get('/charges',                     [ChargeController::class, 'index'])->name('charges');
        Route::get('/charges/create',              [ChargeController::class, 'create'])->name('charges.create');
        Route::get('/charges/show/{id}',           [ChargeController::class, 'show'])->name('charges.show');
        Route::post('/charges/store',              [ChargeController::class, 'store'])->name('charges.store');
        Route::get('/charges/edit/{id}',           [ChargeController::class, 'edit'])->name('charges.edit');
        Route::put('/charges/update/{id}',         [ChargeController::class, 'update'])->name('charges.update');
        Route::delete('/charges/delete/{id}',      [ChargeController::class, 'destroy'])->name('charges.destroy');

        Route::get('/rural-community',                     [RuralCommunityController::class, 'index'])->name('rural-community');
        Route::get('/rural-community/create',              [RuralCommunityController::class, 'create'])->name('rural-community.create');
        Route::get('/rural-community/show/{id}',           [RuralCommunityController::class, 'show'])->name('rural-community.show');
        Route::post('/rural-community/store',              [RuralCommunityController::class, 'store'])->name('rural-community.store');
        Route::get('/rural-community/edit/{id}',           [RuralCommunityController::class, 'edit'])->name('rural-community.edit');
        Route::put('/rural-community/update/{id}',         [RuralCommunityController::class, 'update'])->name('rural-community.update');
        Route::delete('/rural-community/delete/{id}',      [RuralCommunityController::class, 'destroy'])->name('rural-community.destroy');
        Route::post('/rural-community/updateContract/{id}', [RuralCommunityController::class, 'updateContract'])->name('rural-community.update.contract');


        Route::get('/sistema/backups',      [BackupsController::class, 'index'])->name('backups');
        Route::post('/sistema/backups/create',      [BackupsController::class, 'createBackup'])->name('backups.create');
        Route::delete('/sistema/backups/delete/{backup}',      [BackupsController::class, 'destroy'])->name('backups.destroy');
        Route::delete('/sistema/backups/clear',      [BackupsController::class, 'clear'])->name('backups.clear');
        Route::get('/sistema/backups/download/{backup}',      [BackupsController::class, 'download'])->name('backups.download');

        // Settings
        Route::get('/sistema/configuracion',      [SettingsController::class, 'index'])->name('settings');
        Route::get('/sistema/configuracion/paypal',      [PayPalSettingController::class, 'edit'])->name('settings.paypal.edit');
        Route::post('/sistema/configuracion/paypal/update',      [PayPalSettingController::class, 'update'])->name('settings.paypal.update');
        Route::get('/sistema/configuracion/intereses', [InterestsController::class, 'index'])->name('settings.interest');
        Route::get('/sistema/configuracion/intereses/edit/{id}', [InterestsController::class, 'edit'])->name('settings.interest.edit');
        Route::put('/sistema/configuracion/intereses/update/{id}', [InterestsController::class, 'update'])->name('settings.interest.update');
    });
    Route::post('/notifications/read/{id}',  [NotificationController::class, 'markAsRead']);
    Route::get('/notifications/unread',      [NotificationController::class, 'unread']);

    // Route::post('/notifications/read/{id}',  [NotificationController::class, 'markAsRead']);
    // Route::get('/notifications/unread',      [NotificationController::class, 'unread']);

    //MIDDLEWARE DEMÁS USUARIOS
    Route::middleware(['rol:2,3'])->group(function () {

        // Usuarios
        /*  Route::get('/dashboard', function () {
            return Inertia::render('DashboardBase');
        })->name('dashboard');*/
        // Route::get('/dashboard', [StatisticsController::class, 'show'])->name('dashboard');

    });

    Route::middleware(['rol:3'])->group(function () {
        //Router
        Route::get('/tecnico/routers',                  [TechnicalRouterController::class, 'index'])->name('technical.routers');
        Route::get('/tecnico/routers/show/{id}',        [TechnicalRouterController::class, 'show'])->name('technical.routers.show');

        Route::get('/tecnico/routers/{id}/sync',        [TechnicalRouterController::class, 'sync'])->name('technical.routers.sync');
        Route::get('/tecnico/routers/ping/{id}',     [TechnicalRouterController::class, 'sendPing'])->name('technical.routers.ping');

        // -- devices
        Route::get('/tecnico/routers/{router}/devices',     [TechnicalRouterController::class, 'devices'])->name('technical.routers.devices');
        Route::get('/tecnico/devices',                  [TechnicalDevicesController::class, 'index'])->name('technical.devices');
        Route::get('/tecnico/devices/show/{id}',                  [TechnicalDevicesController::class, 'show'])->name('technical.devices.show');
        // -- device red
        Route::patch('/tecnico/devices/set/device/status/{device}',     [TechnicalDevicesController::class, 'setDeviceStatus'])->name('technical.devices.set.status');
        Route::patch('/tecnico/devices/all/set/device/status/{device}',     [TechnicalDevicesController::class, 'AllsetDeviceStatus'])->name('technical.devices.all.set.status');
        Route::get('/tecnico/devices/set/device/ping/{device}',  [TechnicalDevicesController::class, 'sendPing'])->name('technical.devices.ping');

        Route::put('/tecnico/devices/update/{device}',          [TechnicalInventorieDevicesController::class, 'update'])->name('technical.devices.update');
        Route::put('/tecnico/devices/all/update/{device}',          [TechnicalInventorieDevicesController::class, 'device_all_update'])->name('technical.devices.all.update');

        Route::get('/tecnico/devices/all/set/device/ping/{device}',  [TechnicalDevicesController::class, 'sendAllPing'])->name('technical.devices.one.ping');

        // -- ping devices all
        Route::get('/tecnico/devices/{router}/ping',                  [TechnicalDevicesController::class, 'pingAllDevice'])->name('technical.devices.all.ping');

        // Invetorie
        Route::get('/tecnico/inventorie/devices',                 [TechnicalInventorieDevicesController::class, 'index'])->name('technical.inventorie.devices.index');
        Route::get('/tecnico/inventorie/devices/show/{id}', [TechnicalInventorieDevicesController::class, 'show'])->name('technical.inventorie.devices.show');
        // Inventorie histories 
        Route::get('/tecnico/inventorie/devices/histories',          [TechnicalDeviceHistoriesController::class, 'index'])->name('technical.historieDevices.index');

        // Tickets
        Route::get('/tecnico/tickets',                    [TechnicalTicketController::class, 'index'])->name('technical.tickets');
        Route::post('/tecnico/tickets/statusUpdate/{id}', [TechnicalTicketController::class, 'statusUpdate'])->name('technical.tickets.statusUpdate');
        Route::get('/tecnico/tickets/create',             [TechnicalTicketController::class, 'create'])->name('technical.tickets.create');
        Route::get('/tecnico/tickets/show/{id}',          [TechnicalTicketController::class, 'show'])->name('technical.tickets.show');
        Route::get('/tecnico/tickets/create',             [TechnicalTicketController::class, 'create'])->name('technical.tickets.create');
        Route::post('/tecnico/tickets/store',             [TechnicalTicketController::class, 'store'])->name('technical.tickets.store');
        Route::get('/tecnico/tickets/edit/{id}',          [TechnicalTicketController::class, 'edit'])->name('technical.tickets.edit');
        Route::put('/tecnico/tickets/update/{id}',        [TechnicalTicketController::class, 'update'])->name('technical.tickets.update');
        // Route::delete('/tecnico/tickets/delete/{id}',    [TicketController::class, 'destroy'])->name('tickets.destroy');
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
        Route::get('/tickets/usuario',                 [TicketController::class, 'index_user'])->name('tickets.usuario');
        Route::post('/tickets/store/usuario',          [TicketController::class, 'store'])->name('tickets.usuario.store');
    });


    Route::get('/pagos',                     [PayController::class, 'index'])->name('pays');
});
