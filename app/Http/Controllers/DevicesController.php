<?php

namespace App\Http\Controllers;

use App\Models\Device;
use App\Models\Router;
use App\Models\RouterosAPI;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Redirect;

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
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
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


    public function setDeviceStatus(Device $device)
    {
        $device = Device::findOrFail($device->id);
        $router = $device->router;

        $state = 1;
        $action = "";
        $commandAction = "";


        if ($device->disabled) {
            $state = 0;
            $commandAction = "no";
            $action = "activar";
        } else {
            $state = 1;
            $commandAction = "yes";
            $action = "desactivar";
        }



        $device->disabled = $state;

        ///////////////////////////////////////////////////////////////////////
        $router = Router::findOrFail($router->id);

        $ip = $router->ip_address;
        $user = $router->user;
        $password = Crypt::decrypt($router->password);

        $API = new RouterosAPI();

        $API->debug(false);
        //////////////////////////////////////////////////////////////////////////

        //dd($device->device_internal_id);
        if ($API->connect($ip, $user, $password)) {
            $API->comm('/ip/firewall/address-list/set', [
                '.id' => $device->device_internal_id,
                'disabled' => $commandAction
            ]);

            $device->disabled = $state;
            $device->update();
        } else {
            $message = 'Falla al ' . $action . ' el dispositivo, intentalo más tarde';
            return Redirect::route('routers.devices')->with('error', $message);
        }

        $message = $action . ' ' . $device->address . ' realizado con éxito';
        return Redirect::route('routers.devices', $router)->with('success', $message);
    }
}
