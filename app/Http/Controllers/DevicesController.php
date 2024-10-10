<?php

namespace App\Http\Controllers;

use App\Exports\GenericExport;
use App\Http\Requests\Device\StoreDeviceRequest;
use App\Http\Requests\Device\UpdateDeviceRequest;
use App\Models\Device;
use App\Models\DeviceHistorie;
use App\Models\ExtraCharge;
use App\Models\InventorieDevice;
use App\Models\Router;
use App\Models\User;
use App\Models\DeviceStatus;
use App\Models\PingDeviceHistorie;
use App\Services\RouterOSService;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Maatwebsite\Excel\Facades\Excel;

class DevicesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
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
                    ->orWhere('device_id', 'like', "%$search%")
                    ->orWhere('comment', 'like', "%$search%")
                    ->orWhere('address', 'like', "%$search%")
                    ->orWhere('disabled', 'like', "%$search%");
            });
        }

        // Ordenación
        if ($request->attribute) {
            $query->orderBy($request->attribute, $request->order);
        } else {
            $query->orderBy('id', 'asc');
        }

        // Paginación
        $devices = $query->paginate(8)->through(function ($item) {
            return [
                'id' => $item->id,
                //'device_internal_id' => $item->device_internal_id,
                'device_id' => $item->inventorieDevice,
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

        return Inertia::render('Admin/AllDevices/Index', [
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
    public function edit(Router $router, Device $device, $path = 'Admin/Devices/Edit')
    {
        $device = Device::findOrFail($device->id);

        $devices = Device::select('comment', 'address')->get();

        $users = User::select('id', 'name', 'email')->where('admin', '==', '0')->get();

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


            return redirect()->route($url, ['router' => $device->router_id])
                ->with('success', 'El dispositivo ha sido actualizado con éxito');
        } catch (Exception $e) {
            return redirect()->route($url, ['router' => $device->router_id])
                ->with('error', 'Error al intentar conectar con el router, inténtalo más tarde');
        }
    }

    public function device_all_update(UpdateDeviceRequest $request, $id, $url = 'devices')
    {
        return $this->update($request, $id, $url);
    }


    public function destroy($id, $url = 'routers.devices')
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

                return redirect()->route($url, ['router' => $device->router_id])
                    ->with('success', 'El dispositivo ha sido eliminado con éxito');
            } catch (\Exception $e) {
                return redirect()->route($url, ['router' => $device->router_id])
                    ->with('error', 'Error al eliminar la dirección: ' . $e->getMessage());
            } finally {
                $routerOSService->disconnect();
            }
        } catch (\Exception $e) {
            return redirect()->route($url, ['router' => $device->router_id])
                ->with('error', 'Error al intentar conectar con el router, inténtalo más tarde');
        }
    }
    public function device_all_destroy($id, $url = 'devices')
    {
        return $this->destroy($id, $url);
    }
    public function pingAllDevice(Router $router)
    {
        $device = Device::where('router_id', $router->id)
                ->where('disabled', '!=', 1)
                ->get();
       // dd($device);
        $pingController = new PingDeviceHistorieController();
        $pingDevice = new PingDeviceHistorie();
        $count = 0;
        $fail = 0;
        $status = new DeviceStatus();
        $devicesStatus = [];
        try{
            $API = RouterOSService::getInstance();
            $API->connect($router->id);
            foreach($device as $d)
            {
                    $params = [
                        'address' => $d->address,  // Dirección IP del dispositivo al que deseas hacer ping
                        'count' => '4'     // Número de paquetes a enviar
                    ];
    
                    $result = $API->executeCommand('/ping', $params);
                    
                    foreach ($result as $ping) {
                        
                        if(!isset($ping['status']))
                        {
                           $count++;
                        }
                    }
                    $message = '';
            
                    switch($count)
                    {
                        case 0:
                            $message = "Perdida total de paquetes";
                            $fail++;
                            break;
                        case 1:
                            $message = "3 paquetes perdidos";
                            $fail++;
                            break;
                        case 2:
                            $message = "2 paquetes perdidos";
                            $fail++;
                            break;
                        case 3:
                            $message = "1 paquete perdido";
                            break;
                        case 4:
                            $message = "Se han recibido todos lo paquetes exitosamente";
                            break;    
                    }
                    $pingDevice->device_id = $d->id;
                    $pingDevice->router_id = $router->id;
                    $pingDevice->status = $message;
                    $pingController->create($pingDevice);
                    //dd('Primer ping: '.$d -> id);
                 
            }
            $API->disconnect();
            
            if($fail != 0)
            {   
                return Redirect::route('routers.devices',['router' => $router->id])
                ->with('success', 'Se encontraron fallas en alguno de los dispositivos');
            }else{
                return Redirect::route('routers.devices',['router' => $router->id])
                ->with('success', 'Todos los dispositivos operan correctamente');
            }

        }catch(Exception $e)
        {
            return Redirect::route('routers.devices',['router' => $router->id])->with('error', 'Se produjo un error: '.$e);
        }
      
    }
    public function sendPing(Device $device)
    {

        $device = Device::findOrFail($device->id);
        $router = $device->router;

        $router = Router::findOrFail($router->id);

        try
        {
            $API = RouterOSService::getInstance();

            $API ->connect($router->id);
            
            $param = [
                'address' => $device->address,
                'count' => '4'
            ];
            $count = 0;

            $result = $API->executeCommand('/ping', $param);
            
            foreach ($result as $ping) {
                if(isset($ping['status']))
                {
                    //$this->info($ping['status']);               
                }else{
                    //$this->info("Correcto ping");
                    $count++;
                }
            }
            $message = '';
            
            switch($count)
            {
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
    
            return Redirect::route('routers.devices', ['router' => $device->router_id])
            ->with($type, $message);
        }catch(Exception $e)
        {
            return Redirect::route('routers.devices', ['router' => $device->router_id])
            ->with('error', $e);
        }
    }
    
    public function sendAllPing(Device $device, $url = 'devices')
    {
        return $this->sendPing($device, $url);
    }

    public function setDeviceStatus(Device $device, $url = 'routers.devices')
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
            return Redirect::route($url)->with('error', $message);
        }

        $message = $action . ' ' . $device->address . ' realizado con éxito';
        return Redirect::route($url, $router)->with('success', $message);
    }

    public function AllsetDeviceStatus(Device $device, $url = 'devices')
    {
        return $this->setDeviceStatus($device, $url);
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
}
