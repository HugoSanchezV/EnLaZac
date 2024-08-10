<?php

namespace App\Http\Controllers;

use App\Http\Requests\Router\StoreRouterRequest;
use App\Http\Requests\Router\UpdateRouterRequest;
use App\Models\Device;
use App\Models\Router;
use App\Models\RouterosAPI;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;

class RouterController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {

        $query = Router::query();

        // if ($request->type !== null && $request->type !== 'todos') {
        //      $query->where('admin', '=', $request->type);
        // }

        // $query->where('admin', '!=', 1);

        if ($request->has('q')) {
            $search = $request->input('q');
            $query->where(function ($q) use ($search) {
                $q->where('user', 'like', "%$search%")
                    ->orWhere('id', 'like', "%$search%")
                    ->orWhere('ip_address', 'like', "%$search%");
            });
        }

        if ($request->order) {
            $query->orderBy($request->order, 'asc');
        } else {
            $query->orderBy('id', 'asc');
        }

        $routers = $query->latest()->paginate(8)->through(function ($item) {
            return [
                'sync' => $item->sync,
                'id' => $item->id,
                'user' => $item->user,
                'ip_address' => $item->ip_address,
            ];
        });

        $totalRoutersCount = Router::count();

        return Inertia::render('Admin/Routers/Index', [
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
        ]);

        return Inertia::render('Admin/Routers/Index');
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
    }

    /**
     * Show the form for editing the specified resource.
     */
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
            $validatedData['password'] = Hash::make($validatedData['password']);
        } else {
            unset($validatedData['password']);
        }

        $router->update($validatedData);
        return redirect()->route('routers')->with('success', 'Router Actualizado Con Éxito');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $router = Router::findOrFail($id);
        $router->delete();
        return Redirect::route('routers')->with('success', 'Router Eliminado Con Éxito');
    }

    public function sync($id)
    {
        $router = Router::findOrFail($id);

        $ip = $router->ip_address;
        $user = $router->user;
        $password = Crypt::decrypt($router->password);

        $API = new RouterosAPI();

        $API->debug(false);

        if ($API->connect($ip, $user, $password)) {
            $users = $API->comm('/ip/firewall/address-list/print', [
                '.proplist' => '.id,list,address,creation-time,disabled,comment',
                '?list' => 'MOROSOS'
            ]);

            //dd($users);
            
            foreach ($users as $user) {

                $ends = substr($user['address'], -4);

                if (substr($ends, -4) !== '.com' && substr($ends, -4) !== '.net') {

                    $comment = isset($user['comment']) ? $user['comment'] : null;
                    
                    Device::create(
                        [
                            "device_internal_id" => $user[".id"],
                            "router_id" => $id,
                            //"device_id"=> null,
                            //"user_id"=>$user[""],
                            "comment" => $comment,
                            "list" => $user["list"],
                            "address" => $user["address"],
                            "creation_time" => DateTime::createFromFormat('M/d/Y H:i:s', $user["creation-time"]),
                            "disabled" => $user["disabled"] === "false" ? 0 : 1,
                        ]
                    );
                }
            }
        } else {
            return Redirect::route('routers')->with('error', 'No se ha podido sincronizar el Router, intentalo más tarde');
        }

        $router->sync = 1;
        $router->save();

        return Redirect::route('routers')->with('success', 'Router Sincronizado con Éxito');
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
                ->orWhere('device_id', 'like', "%$search%")
                ->orWhere('comment', 'like', "%$search%")
                ->orWhere('address', 'like', "%$search%")
                ->orWhere('disabled', 'like', "%$search%");
            });
        }
        
        // Ordenación
        if ($request->order) {
            $query->orderBy($request->order, 'asc');
        } else {
            $query->orderBy('id', 'asc');
        }
        
        // Paginación
        $devices = $query->paginate(8)->through(function ($item) {
            return [
                'id' => $item->id,
                'device_internal_id' => $item->device_internal_id,
                'router_id' => $item->router_id,
                'device_id' => $item->device_id,
                'user_id' => $item->user_id,
                'comment' => $item->comment,
                'list' => $item->list,
                'address' => $item->address,
                'disabled' => $item->disabled,
            ];
        });
        
        //dd($devices);
        // Conteo total de dispositivos
        $totalDevicesCount = $router->devices()->count();
        
        return Inertia::render('Admin/Routers/Devices', [
            'devices' => $devices,
            'pagination' => [
                'links' => $devices->links()->elements[0],
                'next_page_url' => $devices->nextPageUrl(),
                'prev_page_url' => $devices->previousPageUrl(),
                'per_page' => $devices->perPage(),
                'total' => $devices->total(),
            ],
            'success' => session('success') ?? null,
            'totalDevicesCount' => $totalDevicesCount,
        ]);
    }
}
