<?php

namespace App\Http\Controllers;

use App\Http\Requests\Device\StoreDeviceRequest;
use App\Models\Device;
use App\Models\Router;
use App\Models\RouterosAPI;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
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
                'initial_device_ip' => '172.17.24.'
            ]
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreDeviceRequest $request)
    {
        $validatedData = $request->validated();

        //dd($request);
        //dd($validatedData['address']);

        $router = Router::findOrFail($request->router_id);

        $ip = $router->ip_address;
        $user = $router->user;
        $password = Crypt::decrypt($router->password);

        $API = new RouterosAPI();

        $API->debug(false);

        if ($API->connect($ip, $user, $password)) {
            $response = $API->comm('/ip/firewall/address-list/add', [
                'list' => 'MOROSOS',
                'address' => $validatedData['address'],
                'comment' => $validatedData['comment'],
                //'disable' => 'yes'
            ]);


            if (isset($response['.id'])) {
                $device = Device::create([
                    'device_internal_id' => $response['.id'],
                    'router_id' => $validatedData['router_id'],
                    'device_id' => $validatedData['device_id'] ?? null,
                    'user_id' => $validatedData['user_id'] ?? null,
                    'comment' => $validatedData['comment'],
                    'address' => $validatedData['address'],
                    'list' => 'MOROSOS',
                    'disabled' => true,

                ]);

                return redirect()->route('routers.devices', [
                    'router' => $validatedData['router_id'],
                    'success' => 'El dispositovo ha sido agreado con éxito'
                ]);
            } else {
                return Redirect::route('routers.devices', [
                    'router' => $validatedData['router_id'],
                    'error' => 'Error al añadir la dirección, intentalo más tarde'
                ]);
            }
        } else {
            return Redirect::route('routers.devices', [
                'router' => $validatedData['router_id'],
                'error' => 'Error al intentar conectar con el router, intentalo más tarde'
            ]);
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
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
