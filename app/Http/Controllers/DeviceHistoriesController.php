<?php

namespace App\Http\Controllers;

use App\Models\DeviceHistorie;
use App\Models\InventorieDevice;
use Illuminate\Http\Request;
use Exception;
use Inertia\Inertia;

class DeviceHistoriesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    // public function index2($device_id = null)
    // {
    //     //
    //     $histories = null;
    //     if (isset($device_id)) {
    //         $histories =  DeviceHistorie::where('device_id', $device_id)->with('inventorieDevice', 'user')->get;
    //     } else {
    //         // dd('Entraste al nuevo metodo para ver el historial ');
    //         $histories =  DeviceHistorie::with(['inventorieDevice:id,mac_address', 'user:id,name', 'creator:id,name'])->get();
    //         dd($histories);
    //     }

    //     return Inertia::render('Admin/DeviceHistories/Index', $histories);
    // }

    public function index(Request $request)
    {
        $query = DeviceHistorie::with(['inventorieDevice:id,mac_address', 'user:id,name', 'creator:id,name']);

        if ($request->has('q')) {
            $search = $request->input('q');
            $query->where(function ($q) use ($search) {
                $q->where('id', 'like', "%$search%")
                    ->orWhere('comment', 'like', "%$search%")
                    ->orWhereHas('inventorieDevice', function ($q) use ($search) {
                        $q->where('mac_address', 'like', "%$search%");
                    })
                    ->orWhereHas('user', function ($q) use ($search) {
                        $q->where('name', 'like', "%$search%");
                    })
                    ->orWhereHas('creator', function ($q) use ($search) {
                        $q->where('name', 'like', "%$search%");
                    });
            });
        }

        // Ordenación
        if ($request->attribute) {
            $query->orderBy($request->attribute, $request->order);
        } else {
            $query->orderBy('id', 'asc');
        }

        // Paginación
        $histories = $query->paginate(8)->through(function ($item) {
            return [
                'id' => $item->id,
                //'device_internal_id' => $item->device_internal_id,
                'comment' => $item->comment,
                'device' => $item->inventorieDevice,
                'user' => $item->user,
                'creator' => $item->creator,
            ];
        });

        // Otros datos adicionales (usuarios y dispositivos de inventario)
        //$users = User::where('admin', '0')->select('id', 'name')->get()->makeHidden('profile_photo_url');
        //$inv_devices = InventorieDevice::where('state', '0')->select('id', 'mac_address')->get();

        return Inertia::render('Admin/DeviceHistories/Index', [
            'histories' => $histories,
            'pagination' => [
                'links' => $histories->links()->elements[0],
                'next_page_url' => $histories->nextPageUrl(),
                'prev_page_url' => $histories->previousPageUrl(),
                'per_page' => $histories->perPage(),
                'total' => $histories->total(),
            ],
            'success' => session('success') ?? null,
            'error' => session('error') ?? null,
            'warning' => session('warning') ?? null,
            //'totalDevicesCount' => 4,
            // 'users' => $users,
            // 'inv_devices' => $inv_devices,
        ]);
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
