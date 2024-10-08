<?php

namespace App\Http\Controllers;

use App\Models\DeviceHistorie;
use App\Models\InventorieDevice;
use Exception;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DeviceHistoriesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index($device_id = null)
    {
        //
        $histories = null;
        if (isset($device_id)) {
            $histories =  DeviceHistorie::where('device_id', $device_id);
        } else {
            $histories = DeviceHistorie::all();
        }

        return Inertia::render('', $histories);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store($request)
    {
        try {
            DeviceHistorie::create($request);
        } catch (Exception $e) {
            throw new \Exception('Error al guargar la historia');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(DeviceHistorie $deviceHistorie)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DeviceHistorie $deviceHistorie)
    {
        try {
            $deviceHistorie->destroy;
        } catch (Exception $e) {
            throw new \Exception('Error al actulizar la historia');
        }
    }
}
