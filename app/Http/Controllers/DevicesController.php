<?php

namespace App\Http\Controllers;

use App\Http\Requests\Device\StoreDeviceRequest;
use App\Http\Requests\Device\UpdateDeviceRequest;
use App\Models\Device;
use App\Models\DeviceHistorie;
use App\Models\InventorieDevice;
use App\Models\Router;
use App\Models\RouterosAPI;
use App\Models\User;
use App\Services\RouterOSService;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;

class DevicesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Router $router)
    {
        $devices = Device::select('comment', 'address')->get();

        $users = User::select('id', 'name', 'email')->where('admin', '==', '0')->get();

        return Inertia::render('Admin/Devices/Create', [
            'devices' => $devices,
            'users' => $users,
            'router' => [
                'user' => $router->user,
                'ip_address' => $router->ip_address,
                'networks' => $router->networks->toArray()
            ]
        ]);
    }

    public function store(StoreDeviceRequest $request)
    {
        $validatedData = $request->validated();

        try {
            DB::transaction(function () use ($validatedData) {

                $routerOSService = RouterOSService::getInstance();
                $routerOSService->connect($validatedData['router_id']);

                $response = $routerOSService->executeCommand('/ip/firewall/address-list/add', [
                    'list' => 'MOROSOS',
                    'address' => $validatedData['address'],
                    'comment' => $validatedData['comment'],
                ]);

                $routerOSService->disconnect();

                if (!empty($response)) {
                    Device::create([
                        'device_internal_id' => $response,
                        'router_id' => $validatedData['router_id'],
                        'device_id' => $validatedData['device_id'] ?? null,
                        'user_id' => $validatedData['user_id'] ?? null,
                        'comment' => $validatedData['comment'],
                        'address' => $validatedData['address'],
                        'list' => 'MOROSOS',
                        'disabled' => false,
                        'creation_time' => now(),
                    ]);

                    $router = Router::findOrFail($validatedData['router_id']);
                    $router->total_devices += 1;
                    $router->enable_devices += 1;
                    $router->save();
                }
            });

            return redirect()->route('routers.devices', ['router' => $request->router_id])
                ->with('success', 'El dispositivo ha sido agregado con éxito');
        } catch (Exception $e) {
            return redirect()->route('routers.devices', ['router' => $request->router_id])
                ->with('error', 'Error al intentar conectar con el router, inténtalo más tarde');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Router $router, Device $device)
    {
        $device = Device::findOrFail($device->id);

        $devices = Device::select('comment', 'address')->get();

        $users = User::select('id', 'name', 'email')->where('admin', '==', '0')->get();

        $inv_devices = InventorieDevice::select('id', 'mac_address')->where('state', '0');

        if ($device->device_id) {
            $inv_devices->orWhere('id', $device->device_id);
        }

        $inv_devices = $inv_devices->get();

        return Inertia::render('Admin/Devices/Edit', [
            'devices' => $devices,
            'users' => $users,
            'router' => [
                'user' => $router->user,
                'ip_address' => $router->ip_address,
                'initial_device_ip' => '172.17.24.'
            ],
            'device' => $device,
            'inv_devices' => $inv_devices
        ]);
    }


    public function update(UpdateDeviceRequest $request, $id)
    {
        //dd('Holaaaaaaaaaaaaaaaaaaaaaaaaaa');
        $validatedData = $request->validated();

        $device = Device::findOrFail($id);

        try {
            DB::transaction(function () use ($device, $validatedData, $request, $id) {

                if (($validatedData['address'] !== $device->address)
                    || ($validatedData['comment'] !== $device->comment)
                ) {
                    $routerOSService = RouterOSService::getInstance();
                    $routerOSService->connect($request->router_id);

                    $routerOSService->executeCommand('/ip/firewall/address-list/set', [
                        '.id' => $device->device_internal_id,
                        'address' => $validatedData['address'],
                        'comment' => $validatedData['comment'],
                        //'disable' => 'yes'
                    ]);

                    $routerOSService->disconnect();
                }

                if (isset($validatedData['device_id'])) {
                    if (isset($device->device_id) && $validatedData['device_id'] !== $device->device_id) {
                        InventorieDevicesController::changeStateDevice($device->device_id, '0');

                        DeviceHistorie::create([
                            'state' => 0,
                            'comment' => 'Se ha modificado el estado',
                            'device_id' => $device->device_id,
                            'user_id' => $device->user_id ?? null,
                            'creator_id' => Auth::id(),
                        ]);
                    }
                    InventorieDevicesController::changeStateDevice($validatedData['device_id'], '1');
                    DeviceHistorie::create([
                        'state' => 1,
                        'comment' => 'Se ha modificado el estado',
                        'device_id' => $validatedData['device_id'],
                        'user_id' => $device->user_id ?? null,
                        'creator_id' => Auth::id(),
                    ]);
                }

                $device->update([
                    'device_id' => $validatedData['device_id'] ?? null,
                    'user_id' => $validatedData['user_id'] ?? null,
                    'comment' => $validatedData['comment'] ?? $device->comment,
                    'address' => $validatedData['address'] ?? $device->address,
                ]);
            });


            return redirect()->route('routers.devices', ['router' => $device->router_id])
                ->with('success', 'El dispositivo ha sido actualizado con éxito');
        } catch (Exception $e) {
            dd($e->getMessage());
            return redirect()->route('routers.devices', ['router' => $device->router_id])
                ->with('error', 'Error al intentar conectar con el router, inténtalo más tarde');
        }
    }


    public function destroy($id)
    {
        $device = Device::findOrFail($id);
        try {
            $routerOSService = RouterOSService::getInstance();
            $routerOSService->connect($device->router_id);

            try {

                DB::transaction(function () use ($device, $routerOSService) {
                    $router =  $device->router;

                    if (!$router->disabled) {
                        $router->enable_devices -= 1;
                    }
                    $router->total_devices -= 1;
                    $router->save();
                    $device->delete();

                    $response = $routerOSService->executeCommand(
                        '/ip/firewall/address-list/remove',
                        [
                            '.id' => $device->device_internal_id,
                        ]
                    );

                    if (isset($response['!trap'])) {
                        throw new \Exception('Error en la eliminación de la dirección en RouterOS');
                    }
                });

                return redirect()->route('routers.devices', ['router' => $device->router_id])
                    ->with('success', 'El dispositivo ha sido eliminado con éxito');
            } catch (\Exception $e) {
                return redirect()->route('routers.devices', ['router' => $device->router_id])
                    ->with('error', 'Error al eliminar la dirección: ' . $e->getMessage());
            } finally {
                $routerOSService->disconnect();
            }
        } catch (\Exception $e) {
            return redirect()->route('routers.devices', ['router' => $device->router_id])
                ->with('error', 'Error al intentar conectar con el router, inténtalo más tarde');
        }
    }

    public function setDeviceStatus(Device $device)
    {
        $device = Device::findOrFail($device->id);
        $router = $device->router;

        $state = 1;
        $action = "";
        $commandAction = "";
        $increment_enable_devices = -1;

        if ($device->disabled) {
            $state = 0;
            $commandAction = "no";
            $action = "activar";
            $increment_enable_devices = 1;
        } else {
            $state = 1;
            $commandAction = "yes";
            $action = "desactivar";
        }

        $device->disabled = $state;

        ///////////////////////////////////////////////////////////////////////
        $router = Router::findOrFail($router->id);

        //////////////////////////////////////////////////////////////////////////

        try {
            $routerOSService = RouterOSService::getInstance();
            $routerOSService->connect($router->id);

            DB::transaction(function () use (
                $routerOSService,
                $device,
                $router,
                $commandAction,
                $state,
                $increment_enable_devices,
            ) {
                $routerOSService->executeCommand(
                    '/ip/firewall/address-list/set',
                    [
                        '.id' => $device->device_internal_id,
                        'disabled' => $commandAction
                    ]
                );

                $routerOSService->disconnect();

                $device->disabled = $state;
                $router->enable_devices += $increment_enable_devices;
                $router->save();
                $device->update();
            });
        } catch (\Exception $e) {
            $message = 'Falla al ' . $action . ' el dispositivo, intentalo más tarde';
            return Redirect::route('routers.devices')->with('error', $message);
        }

        $message = $action . ' ' . $device->address . ' realizado con éxito';
        return Redirect::route('routers.devices', $router)->with('success', $message);
    }
}
