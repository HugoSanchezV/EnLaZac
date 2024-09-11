<?php

namespace App\Http\Controllers;

use App\Models\DeviceHistorie;
use App\Models\InventorieDevice;
use Exception;
use Illuminate\Http\Request;

class DeviceHistoriesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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
