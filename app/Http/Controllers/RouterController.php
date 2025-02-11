<?php

namespace App\Http\Controllers;

use App\Exports\GenericExport;
use App\Http\Requests\Router\StoreRouterRequest;
use App\Http\Requests\Router\UpdateRouterRequest;
use App\Imports\RouterImport;
use App\Models\Device;
use App\Models\InventorieDevice;
use App\Models\Network;
use App\Models\Router;
use App\Models\User;
use App\Services\RouterOSService;
use App\Services\RouterService;
use DateTime;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Maatwebsite\Excel\Facades\Excel;


class RouterController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    protected $routerService;
    protected $path = 'Admin';


    public function __construct(RouterService $routerService)
    {
        $this->routerService = $routerService;
        if (Auth::user()->admin === 2) {
            $this->path = 'Coordi';
        }
    }

    public function index(Request $request)
    {
        $query = Router::query();

        if ($request->has('q')) {
            $search = $request->input('q');
            $query->where(function ($q) use ($search) {
                $q->where('user', 'like', "%$search%")
                    ->orWhere('id', 'like', "%$search%")
                    ->orWhere('ip_address', 'like', "%$search%");
            });
        }

        // if ($request->attribute) {
        //     $query->orderBy($request->attribute, $request->order);
        // } else {
        //     $query->orderBy('id', 'asc');
        // }
        $order = 'asc';
        if ($request->order && in_array(strtolower($request->order), ['asc', 'desc'], true)) {
            $order = strtolower($request->order);
        }
        $query->orderBy(
            $request->attribute ?: 'id',
            $order
        );


        $routers = $query->latest()->paginate(8)->through(function ($item) {
            return [
                'sync' => $item->sync,
                'id' => $item->id,
                'user' => $item->user,
                'ip_address' => $item->ip_address,
                'total_devices' => $item->total_devices,
                'enable_devices' => $item->enable_devices,
            ];
        });
        $scheduleTask = new ScheduledTaskController();
        $schedule = $scheduleTask->status('ping-routers');
        // dd($schedule);
        $totalRoutersCount = Router::count();
        //Admin/Routers/Index
        return Inertia::render($this->path . '/Routers/Index', [
            'routers' => $routers,
            'pagination' => [
                'links' => $routers->links()->elements[0],
                'next_page_url' => $routers->nextPageUrl(),
                'prev_page_url' => $routers->previousPageUrl(),
                'per_page' => $routers->perPage(),
                'total' => $routers->total(),
            ],
            'success' => session('success') ?? null,
            'totalRoutersCount' => $totalRoutersCount,
            'schedule' => $schedule,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
        return Inertia::render('Admin/Routers/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRouterRequest $request)
    {
        //
        $validatedData = $request->validated();

        $router = Router::create([
            'ip_address' => $validatedData['ip_address'],
            'user' => $validatedData['user'],
            'password' => Crypt::encrypt($validatedData['password']),
        ]);


        return redirect()->route('routers')->with('success', 'Router registrado con éxito');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $router = Router::findOrFail($id);

        return Inertia::render('Admin/Routers/Show', [
            'router' => $router,
        ]);
    }


    public function edit($id)
    {
        $router = Router::findOrFail($id);
        return Inertia::render('Admin/Routers/Edit', [
            'router' => $router
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRouterRequest $request, $id)
    {
        $router = Router::findOrFail($id);
        $validatedData = $request->validated();

        if (!empty($validatedData['password'])) {
            $validatedData['password'] = Crypt::encrypt($validatedData['password']);
        } else {
            unset($validatedData['password']);
        }

        $router->update($validatedData);
        return redirect()->route('routers')->with('success', 'Router Actualizado Con Éxito');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, $id)
    {
        try {
            DB::transaction(function () use ($id) {
                $router = Router::findOrFail($id);


                $device = Device::with("inventorieDevice")
                    ->select('device_id')
                    ->where('router_id', $id)
                    ->whereNotNull('device_id')
                    ->get();

                if ($device->isNotEmpty()) {
                    foreach ($device as $item) {
                        InventorieDevicesController::changeStateDevice($item->device_id, 0);
                    }
                }

                $router->delete();
            });

            return Redirect::route('routers', $request->query())->with('success', 'Router Eliminado Con Éxito');
        } catch (Exception $e) {
            return Redirect::route('routers', $request->query())->with('error', 'Error al cargar el registro');
        }
    }

    public function sendPing($id)
    {
        $router = Router::findOrFail($id);
        $ip = $router->ip_address;
        // $message = "Hola";
        try {

            $routerOSService = RouterOSService::getInstance();
            if ($routerOSService->connectMessage($id)) {
                $routerOSService->disconnect();

                return Redirect::route('routers')->with('success', "El dispositivo está en línea.");
            } else {
                return Redirect::route('routers')->with('error', "El dispositivo se encuentra fuera de línea");
                //return "Offline";
            }
        } catch (Exception $e) {
            return Redirect::route('routers')->with('error', $e->getMessage());
        }
    }
    public function sync($id)
    {
        try {
            DB::transaction(function () use ($id) {
                $routerOSService = RouterOSService::getInstance();
                $routerOSService->connect($id);

                $router = Router::lockForUpdate()->findOrFail($id);

                $address = $routerOSService->executeCommand('/ip/address/print');


                $address = $this->routerService->filterNetworksByPrefix($address, '172.17');

                $users = $routerOSService->executeCommand(
                    '/ip/firewall/address-list/print',
                    [
                        '.proplist' => '.id,list,address,creation-time,disabled,comment',
                        '?list' => 'MOROSOS'
                    ]
                );

                // dd($users);

                $db_devices = Device::where('router_id', $id)->get();

                if (!empty($users) && !empty($db_devices)) {
                    $users = $this->routerService->getDevicesNotInDatabase($users, $db_devices);

                    if (empty($users)) {
                        return Redirect::route('routers')->with('success', 'El router se encuntra actualizado, todos los id conciden con la base de datos');
                    }
                }

                $total_devices = count($users);
                $enable_devices = 0;

                foreach ($users as $user) {

                    $comment = isset($user['comment']) ? mb_convert_encoding($user['comment'], 'UTF-8', 'auto')  : null;

                    $enable_devices += $user["disabled"] === "false" ? 1 : 0;

                    Device::create(
                        [
                            "device_internal_id" => $user[".id"],
                            "router_id" => $id,
                            //"device_id"=> null,
                            //"user_id"=>$user[""],
                            "comment" => $comment,
                            "list" => $user["list"],
                            "address" => $user["address"],
                            "creation_time" => now(),
                            "disabled" => $user["disabled"] === "false" ? 0 : 1,
                        ]
                    );
                }

                $routerOSService->disconnect();


                $router->sync = 1;
                $router->total_devices = $total_devices;
                $router->enable_devices = $enable_devices;
                $router->save();

                $db_networks = $router->networks;
                $networks = $this->routerService->getNetworksNotInDatabase($address, $db_networks->toArray());

                foreach ($networks as $network) {
                    Network::create([
                        'router_id' => $router->id,
                        'address' => $network["address"],
                        'network' => $network["network"],
                    ]);
                }
            });

            return Redirect::route('routers')->with('success', 'Router Sincronizado con Éxito');
        } catch (Exception $e) {
            // dd($e);
            if ($e->getCode() === "HY000") {
                dd($e);
            }
            return Redirect::route('routers')->with('error', $e->getMessage());
        }
    }

    public function listDevices2(Router $router)
    {
        $devices = $router->devices()->get()->toArray();
        return Inertia::render('Admin/Routers/DeviceTable',  [
            'devices' => $devices
        ]);
    }

    public function devices(Router $router, Request $request)
    {
        // aqui no se usa query por que el has many no tiene,
        // por lo tanto podemos usar durecto el router -> devices 
        $query = $router->devices();

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

        $order = 'asc';
        if ($request->order && in_array(strtolower($request->order), ['asc', 'desc'], true)) {
            $order = strtolower($request->order);
        }
        $query->orderBy(
            $request->attribute ?: 'id',
            $order
        );

        // Paginación
        $devices = $query
            ->with('inventorieDevice:id,mac_address', 'inventorieDevice.contract')
            ->with('user:id,name')
            ->paginate(8)
            ->through(function ($item) {

                //$item->user->makeHidden('profile_photo_url');

                return [
                    'id' => $item->id,
                    'device_internal_id' => $item->device_internal_id,
                    //'router_id' => $item->router_id,
                    'device_id' => $item->inventorieDevice,
                    'user_id' => $item->user,
                    'comment' => $item->comment,
                    //'list' => $item->list,
                    'address' => $item->address,
                    'disabled' => $item->disabled,
                ];
            });

        //dd($devices);
        // Conteo total de dispositivos
        $totalDevicesCount = $router->devices()->count();
        $users = User::where('admin', '0')->select('id', 'name')->get()->makeHidden('profile_photo_url');
        $inv_devices = InventorieDevice::where('state', '0')->select('id', 'mac_address')->get();


        return Inertia::render($this->path . '/Routers/Devices', [
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
            'totalDevicesCount' => $totalDevicesCount,
            'users' => $users,
            'inv_devices' => $inv_devices,
            'router' => $router->id,

        ]);
    }

    public function exportExcel()
    {
        $query = Router::query();

        $headings = [
            'ID',
            'Usuario',
            'IP',
            'Dispositivos',
            'Disp. Activos',
        ];

        $mappingCallback = function ($contract) {
            return [
                'id' => $contract->id,
                'usuario' => $contract->user ?? 'None',
                'ip' => $contract->ip_address ?? 'None',
                'dispositivos' => $contract->total_devices ?? 'None',
                'disp. activos' => $contract->enable_devices,
            ];
        };

        return Excel::download(new GenericExport($query, $headings, $mappingCallback), 'routers.xlsx');
    }

    public function devicesExportExcel(Router $router)
    {
        $dataRouter = Router::with(['devices.user', 'devices.inventorieDevice'])->findOrFail($router->id);
        // es el metodo devies que pertenece a router
        $query = $dataRouter->devices;

        $headings = [
            'ID',
            'Internal ID',
            'Device ID',
            'Device Mac',
            'User ID',
            'User Name',
            'Comment',
            'IP Address',
            'Status',
        ];

        $mappingCallback = function ($device) {
            return [
                $device->id,
                $device->device_internal_id ?? 'Sin asignar',
                $device->inventorieDevice->id ?? 'Sin asignar',
                $device->inventorieDevice->mac_address ?? 'Sin asignar',
                $device->user_id ?? 'Sin asignar',
                $device->user->name ?? 'Sin asignar',
                $device->comment,
                $device->address,
                $device->disabled ? 'Inactivo' : 'Activo',
            ];
        };
        return Excel::download(new GenericExport($query, $headings, $mappingCallback), 'Dispositivos de Router ' . $router->ip_address . '.xlsx');
    }

    public function importExcel(Request $request)
    {
        // dd('Inventario de dispositivos');
        try {
            $file = $request->excel;
            Excel::import(new RouterImport, $file);
            return Redirect::route('routers')->with('success', 'Archivo Importado Con Éxito ');
        } catch (ValidationException $e) {
            $failures = $e->failures();
            foreach ($failures as $failure) {
                $rows = $failure->row(); // Fila donde ocurrió el error
                // $attribute = $failure->attribute(); // Nombre del campo con error
                // $errors = $failure->errors(); // Lista de errores para este campo
                // $values = $failure->values(); // Valores originales de esa fila

                // Aquí puedes hacer algo como registrar los errores, mostrarlos al usuario, etc.
                // Por ejemplo, podrías registrar los errores en una variable de sesión o en un log

                return redirect()->back()->with($rows);
            }
        } catch (Exception $e) {
            return Redirect::route('routers')->with('error', 'Error al Importar ' . $e->getMessage());
        }
    }
}
