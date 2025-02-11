<?php

namespace App\Http\Controllers;

use App\Exports\GenericExport;
use App\Http\Requests\Device\StoreDeviceRequest;
use App\Http\Requests\Device\UpdateDeviceRequest;
use App\Imports\AllDeviceImport;
use App\Models\Contract;
use App\Models\Device;
use App\Models\DeviceHistorie;
use App\Models\InventorieDevice;
use App\Models\Router;
use App\Models\User;
use App\Models\DeviceStatus;
use App\Models\PingDeviceHistorie;
use App\Models\Plan;
use App\Services\DeviceService;
use App\Services\RouterOSService;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;
use Maatwebsite\Excel\Facades\Excel;

class DevicesController extends Controller
{
    protected DeviceService $deviceService;
    protected $path = 'Admin';
    // public function __construct()
    // {
    //     $this->deviceService = new DeviceService();
    //     if (Auth::user()->admin == 2) {
    //         $this->path = 'Coordi';
    //     }
    // }

    public function index(Request $request)
    {
        // Trabajamos con Eloquent directamente, sin getQuery()
        $query = Device::with(['inventorieDevice:id,mac_address', 'user:id,name', 'router:id,ip_address']);

        // Filtro por parámetros de búsqueda si existen
        if ($request->has('q')) {
            $search = $request->input('q');
            $query->where(function ($q) use ($search) {
                $q->where('id', 'like', "%$search%")
                    ->orwhere('device_internal_id', 'like', "%$search%")
                    // ->orWhere('device_id', 'like', "%$search%")
                    ->orWhereHas('inventorieDevice', function ($q) use ($search) {
                        $q->where('mac_address', 'like', "%$search%");
                    })
                    ->orWhereHas('user', function ($q) use ($search) {
                        $q->where('name', 'like', "%$search%");
                    })
                    ->orWhere('comment', 'like', "%$search%")
                    ->orWhere('address', 'like', "%$search%")
                    ->orWhere('disabled', 'like', "%$search%");
            });
        }

        // Ordenación
        $order = 'asc';
        if ($request->order && in_array(strtolower($request->order), ['asc', 'desc'], true)) {
            $order = strtolower($request->order);
        }
        $query->orderBy(
            $request->attribute ?: 'id',
            $order
        );

        // Paginación
        $devices = $query->with('inventorieDevice:id,mac_address', 'inventorieDevice.contract')->paginate(8)->through(function ($item) {
            return [
                'id' => $item->id,
                //'device_internal_id' => $item->device_internal_id,
                'device_id' => $item->inventorieDevice->contracts ?? $item->inventorieDevice,
                'user_id' => $item->user,
                'comment' => $item->comment,
                'address' => $item->address,
                'router' => $item->router,
                'disabled' => $item->disabled,
            ];
        });

        // Otros datos adicionales (usuarios y dispositivos de inventario)
        $users = User::where('admin', '0')->select('id', 'name')->get()->makeHidden('profile_photo_url');
        $inv_devices = InventorieDevice::where('state', '0')->select('id', 'mac_address')->get();

        return Inertia::render($this->path . '/AllDevices/Index', [
            'devices' => $devices,
            'pagination' => [
                'links' => $devices->links()->elements[0],
                'next_page_url' => $devices->nextPageUrl(),
                'prev_page_url' => $devices->previousPageUrl(),
                'per_page' => $devices->perPage(),
                'total' => $devices->total(),
            ],
            'success' => session('success') ?? null,
            'error' => session('error') ?? null,
            'warning' => session('warning') ?? null,
            'totalDevicesCount' => Device::count(),
            'users' => $users,
            'inv_devices' => $inv_devices,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Router $router)
    {
        $devices = Device::select('comment', 'address')->get();

        $users = User::select('id', 'name', 'email')->where('admin', '=', '0')->get();

        $inv_devices = InventorieDevice::select('id', 'mac_address')->where('state', '0')->get();

        return Inertia::render('Admin/Devices/Create', [
            'devices' => $devices,
            'users' => $users,
            'inv_devices' => $inv_devices,
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

                    if (isset($validatedData['device_id'])) {
                        InventorieDevicesController::changeStateDevice($validatedData['device_id'], '1');
                        DeviceHistorie::create([
                            'state' => 1,
                            'comment' => 'Se ha modificado el estado a "en uso"',
                            'device_id' => $validatedData['device_id'],
                            'user_id' => $device->user_id ?? null,
                            'creator_id' => Auth::id(),
                        ]);
                    }
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

        $devices = Device::with('user', 'router', 'inventorieDevice')->findOrFail($id);

        return Inertia::render('Admin/Devices/Show', [
            'devices' => $devices,
        ]);
    }

    public function searchID($address)
    {
        $validator = Validator::make(['address' => $address], [
            'address' => ['required', 'ip'],
        ]);

        // Comprobar si la validación falla
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        // Buscar el dispositivo si la validación es exitosa
        $device = Device::where('address', $address)->first();

        return $device ? $device->id : null;
    }
    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Router $router, Device $device, $path = 'Admin/Devices/Edit')
    {
        $device = Device::findOrFail($device->id);

        $devices = Device::select('comment', 'address')->get();

        $users = User::select('id', 'name', 'email')->where('admin', '=', '0')->get();

        $inv_devices = InventorieDevice::select('id', 'mac_address')->where('state', '0');

        if ($device->device_id) {
            $inv_devices->orWhere('id', $device->device_id);
        }

        $inv_devices = $inv_devices->get();

        return Inertia::render($path, [
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

    public function device_all_edit(Router $router, Device $device, $path = 'Admin/AllDevices/Edit')
    {
        return $this->edit($router, $device, $path);
    }


    public function update(UpdateDeviceRequest $request, $id, $url = 'routers.devices')
    {
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
                            'comment' => 'Se ha modificado el estado a "disponible"',
                            'device_id' => $device->device_id,
                            'user_id' => $device->user_id ?? null,
                            'creator_id' => Auth::id(),
                        ]);
                    }
                    InventorieDevicesController::changeStateDevice($validatedData['device_id'], '1');
                    DeviceHistorie::create([
                        'state' => 1,
                        'comment' => 'Se ha modificado el estado a "en uso"',
                        'device_id' => $validatedData['device_id'],
                        'user_id' => $device->user_id ?? null,
                        'creator_id' => Auth::id(),
                    ]);
                } else {
                    if (isset($device->device_id)) {
                        InventorieDevicesController::changeStateDevice($device->device_id, '0');

                        DeviceHistorie::create([
                            'state' => 0,
                            'comment' => 'Se ha modificado el estado a "disponible"',
                            'device_id' => $device->device_id,
                            'user_id' => $device->user_id ?? null,
                            'creator_id' => Auth::id(),
                        ]);
                    }
                }

                $device->update([
                    'device_id' => $validatedData['device_id'] ?? null,
                    'user_id' => $validatedData['user_id'] ?? null,
                    'comment' => $validatedData['comment'] ?? $device->comment,
                    'address' => $validatedData['address'] ?? $device->address,
                ]);
            });

            return redirect()->route($url, [
                "router" => $device->router_id,
                "q" => $request->q,
                "attribute" => $request->attribute,
                "order" => $request->order,
            ])
                ->with('success', 'El dispositivo ha sido actualizado con éxito');
        } catch (Exception $e) {
            return redirect()->route($url, [
                "router" => $device->router_id,
                "q" => $request->q,
                "attribute" => $request->attribute,
                "order" => $request->order,
            ])
                ->with('error', 'Error al intentar conectar con el router, inténtalo más tarde');
        }
    }

    public function device_all_update(UpdateDeviceRequest $request, $id, $url = 'devices')
    {
        return $this->update($request, $id, $url);
    }


    public function destroy(Request $request, $id, $url = 'routers.devices')
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

                return redirect()->route($url, [
                    "router" => $device->router_id,
                    "q" => $request->q,
                    "attribute" => $request->attribute,
                    "order" => $request->order,
                ])
                    ->with('success', 'El dispositivo ha sido eliminado con éxito');
            } catch (\Exception $e) {
                return redirect()->route($url, [
                    "router" => $device->router_id,
                    "q" => $request->q,
                    "attribute" => $request->attribute,
                    "order" => $request->order,
                ])
                    ->with('error', 'Error al eliminar la dirección: ' . $e->getMessage());
            } finally {
                $routerOSService->disconnect();
            }
        } catch (\Exception $e) {
            return redirect()->route($url, [
                "router" => $device->router_id,
                "q" => $request->q,
                "attribute" => $request->attribute,
                "order" => $request->order,
            ])
                ->with('error', 'Error al intentar conectar con el router, inténtalo más tarde');
        }
    }
    public function device_all_destroy(Request $request, $id, $url = 'devices')
    {
        return $this->destroy($request, $id, $url);
    }
    public function pingAllDevice(Router $router)
    {
        $devices = Device::where('router_id', $router->id)
            ->where('disabled', '!=', 1)
            ->get();

        $failedDevices = 0;
        $totalDevices = $devices->count();
        $pingController = new PingDeviceHistorieController();

        try {
            $API = RouterOSService::getInstance();
            $API->connect($router->id);

            foreach ($devices as $device) {
                $params = [
                    'address' => $device->address,
                    'count' => '4',
                ];

                // Ejecutar comando de ping
                $result = $API->executeCommand('/ping', $params);

                // Contar paquetes exitosos
                $successfulPings = collect($result)->filter(fn($ping) => isset($ping['status']) && $ping['status'] === 'ok')->count();

                // Determinar el estado basado en los paquetes exitosos
                $statusMessage = $this->determinePingStatus($successfulPings);
                if ($successfulPings < 4) {
                    $failedDevices++;
                }

                // Preparar los datos para enviar al método create
                $pingDevice = [
                    'device_id' => $device->id,
                    'router_id' => $router->id,
                    'status' => $statusMessage,
                ];

                // Llamar al método create del controlador PingDeviceHistorieController
                $pingController->create($pingDevice);
            }

            $API->disconnect();

            // Redirección con el mensaje adecuado
            if ($failedDevices > 0) {
                return redirect()->route('routers.devices', ['router' => $router->id])
                    ->with('success', "$failedDevices de $totalDevices dispositivos presentan fallas.");
            }

            return redirect()->route('routers.devices', ['router' => $router->id])
                ->with('success', 'Todos los dispositivos operan correctamente.');
        } catch (Exception $e) {
            Log::error("Error al hacer ping a los dispositivos del router {$router->id}: " . $e->getMessage());

            return redirect()->route('routers.devices', ['router' => $router->id])
                ->with('error', 'Se produjo un error: ' . $e->getMessage());
        }
    }
    private function determinePingStatus(int $successfulPings): string
    {
        return match ($successfulPings) {
            0 => 'Perdida total de paquetes',
            1 => '3 paquetes perdidos',
            2 => '2 paquetes perdidos',
            3 => '1 paquete perdido',
            4 => 'Se han recibido todos los paquetes exitosamente',
            default => 'Estado desconocido',
        };
    }

    public function sendPing(Device $device, $url = 'routers.devices')
    {

        $device = Device::findOrFail($device->id);
        $router = $device->router;

        $router = Router::findOrFail($router->id);

        try {
            $API = RouterOSService::getInstance();

            $API->connect($router->id);

            $param = [
                'address' => $device->address,
                'count' => '4'
            ];
            $count = 0;

            $result = $API->executeCommand('/ping', $param);

            foreach ($result as $ping) {
                if (isset($ping['status'])) {
                    //$this->info($ping['status']);               
                } else {
                    //$this->info("Correcto ping");
                    $count++;
                }
            }
            $message = '';

            switch ($count) {
                case 0:
                    $message = "Perdida total de paquetes";
                    $type = 'error';
                    break;
                case 1:
                    $message = "3 paquetes perdidos";
                    $type = 'warning';
                    break;
                case 2:
                    $message = "2 paquetes perdidos";
                    $type = 'warning';
                    break;
                case 3:
                    $message = "1 paquete perdido";
                    $type = 'warning';
                    break;
                case 4:
                    $message = "Se han recibido todos lo paquetes exitosamente";
                    $type = 'success';
                    break;
            }


            $API->disconnect();

            return Redirect::route($url, ['router' => $device->router_id])
                ->with($type, $message);
        } catch (Exception $e) {
            return Redirect::route($url, ['router' => $device->router_id])
                ->with('error', $e);
        }
    }

    public function sendAllPing(Device $device, $url = 'devices')
    {
        return $this->sendPing($device, $url);
    }

    public function setDeviceStatus(Device $device, Request $request, $url = 'routers.devices')
    {
        $device = Device::lockForUpdate()->findOrFail($device->id);
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
            return Redirect::route($url, [
                "router" => $device->router_id,
                "q" => $request->q,
                "attribute" => $request->attribute,
                "order" => $request->order,
            ])->with('error', $message);
        }

        $message = $action . ' ' . $device->address . ' realizado con éxito';
        return Redirect::route(
            $url,
            [
                "router" =>  $device->router_id,
                "q" => $request->q,
                "attribute" => $request->attribute,
                "order" => $request->order,
            ]
        )->with('success', $message);
    }

    public function AllsetDeviceStatus(Device $device, Request $request, $url = 'devices')
    {
        return $this->setDeviceStatus($device, $request, $url);
    }

    public function disconectUser(Contract $contract)
    {
        try {
            $devices = Device::where('device_id', $contract->inv_device_id)->first();
            if ($devices) {
                // foreach($devices  as $device)
                // {
                if ($devices->disabled == 0) {
                    $devices->disabled = 1;

                    $this->setDeviceStatusContrato($devices);
                }
                // }
            }
        } catch (Exception $e) {
            Log::error($e);
        }
    }
    public function connectUser(Contract $contract)
    {
        try {
            $devices = Device::where('device_id', $contract->inv_device_id)->first();

            // $devices = Device::findOrFail($inv_device->device->id);

            if (!is_null($devices)) {
                // foreach($devices  as $device)
                // {
                if ($devices->disabled == 1) {
                    $devices->disabled = 0;

                    $this->setDeviceStatusContrato($devices);
                }
                // }
            }
        } catch (Exception $e) {
            Log::error($e);
        }
    }

    public function setDeviceStatusContrato(Device $device)
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
            // $message = 'Falla al ' . $action . ' el dispositivo, intentalo más tarde';
            //return Redirect::route('routers.devices')->with('error', $message);
            dd("ERROR AL CAMBIAR EL ESTADO DEL DISPOSITIVO: " + $e);
        }
        //  $message = $action . ' ' . $device->address . ' realizado con éxito';
        //return Redirect::route('routers.devices', $router)->with('success', $message);
    }

    public function allDevicesExportExcel()
    {
        $dataRouter = Device::with(['user', 'inventorieDevice', 'router']);
        // es el metodo devies que pertenece a router
        $query = $dataRouter;


        $headings = [
            'ID',
            'Internal ID',
            'ID Usuario',
            'Usuario',
            'Comentario',
            'ID Inventario',
            'IP Dispositivo',
            'MAC Dispositivo',
            'ID Router',
            'IP Router',
            'Estado',
        ];

        $mappingCallback = function ($device) {
            // dd($device);
            return [
                $device->id,
                $device->device_internal_id ?? 'Sin asignar',
                $device->user->id ?? '-',
                $device->user->name ?? '-',
                $device->comment ?? '',
                $device->inventorieDevice->id ?? '-',
                $device->address ?? '-',
                $device->inventorieDevice->mac_address ?? '-',
                $device->router->id,
                $device->router->ip_address,
                $device->disabled ? 'Inactivo' : 'Activo',
            ];
        };
        return Excel::download(new GenericExport($query, $headings, $mappingCallback), 'Dispositivos de Router.xlsx');
    }


    public function allDevicesImportExcel2(Request $request)
    {
        try {
            $file = $request->excel;
            $local = filter_var($request->local, FILTER_VALIDATE_BOOLEAN);;
            // dd($local);
            Excel::import(new AllDeviceImport($this->deviceService, $local), $file);
            return Redirect::route('devices')->with('success', 'Archivo Importado Con Éxito ');
        } catch (\Exception $e) {
            return Redirect::route('devices')->with('error', 'Error al Importar, ' . $e->getMessage());
        }
    }


    public function setConsumePlanToDevice(Device $device, Plan $plan)
    {
        try {
            $routerOSService = RouterOSService::getInstance();
            $routerOSService->connect($device->router_id);

            $burst_limit = $plan->burst_limit['upload_limits'] . '/' . $plan->burst_limit['download_limits'];
            $burst_threshold = $plan->burst_threshold['upload_limits'] . '/' . $plan->burst_threshold['download_limits'];
            $burst_time = $plan->burst_time['upload_limits'] . '/' . $plan->burst_time['download_limits'];
            $limite_at = ($plan->limite_at['upload_limits']) . '/' . ($plan->limite_at['download_limits']);
            $max_limit = $plan->max_limit['upload_limits'] . '/' . $plan->max_limit['download_limits'];

            $existingQueue = $routerOSService->executeCommand('/queue/simple/print', [
                '?=target' => $device->address . '/32',
                '?=name' => $device->comment,
            ]);

            if (isset($existingQueue[0])) {
                // Si ya existe una cola, la actualizamos
                $queueId = $existingQueue[0]['.id'];
                $response = $routerOSService->executeCommand('/queue/simple/set', [
                    '.id' => $queueId,
                    'burst-limit' => $burst_limit,
                    'burst-threshold' => $burst_threshold,
                    'burst-time' => $burst_time,
                    'limit-at' => $limite_at,
                    'max-limit' => $max_limit,
                    'name' => $device->comment,
                ]);
            } else {
                // Si no existe, la creamos
                $response = $routerOSService->executeCommand('/queue/simple/add', [
                    'burst-limit' => $burst_limit,
                    'burst-threshold' => $burst_threshold,
                    'burst-time' => $burst_time,
                    'limit-at' => $limite_at,
                    'max-limit' => $max_limit,
                    'target' => $device->address,
                    'name' => $device->comment,
                ]);
            }

            if (!isset($response) || isset($response['!trap'])) {
                dd($response);
                throw new Exception('Error al asiganar comsumo en red');
            }
        } catch (Exception $e) {
            throw new Exception('Error en setConsumePlanToDevice: ' . $e->getMessage(), $e->getCode(), $e);
        } finally {
            // Desconexión garantizada
            $routerOSService->disconnect();
        }
    }

    public function removeConsumePlanFromDevice(Device $device)
    {
        try {
            $routerOSService = RouterOSService::getInstance();
            $routerOSService->connect($device->router_id);

            // Buscar la cola correspondiente a la IP del dispositivo
            $existingQueue = $routerOSService->executeCommand('/queue/simple/print', [
                '?=target' => $device->address . '/32',
                '?=name' => $device->comment
            ]);

            if (isset($existingQueue[0])) {
                // Si se encuentra una cola, obtener el ID y eliminarla
                $queueId = $existingQueue[0]['.id'];
                $response = $routerOSService->executeCommand('/queue/simple/remove', [
                    '.id' => $queueId,
                ]);

                if (!isset($response) || isset($response['!trap'])) {
                    throw new Exception('Error al eliminar la cola de consumo en red');
                }
            } else {
                // No se encontró una cola para el dispositivo
                Log::info('No se encontro el consumo de la conexion ' .  $device->address);
            }
        } catch (Exception $e) {
            throw new Exception('Error en removeConsumePlanFromDevice: ' . $e->getMessage(), $e->getCode(), $e);
        } finally {
            // Desconexión garantizada
            $routerOSService->disconnect();
        }
    }

    public function conectarTest()
    {
        try {
            $routerOSService = RouterOSService::getInstance();
            $routerOSService->connect(1);

            $routerOSService->disconnect();
            dd('Se conecto y se desconecto');
        } catch (Exception $e) {
            throw new Exception('Error en setConsumePlantoDevice ' . $e->getmessage());
        }
    }
}
