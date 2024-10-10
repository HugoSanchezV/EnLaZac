<?php

namespace App\Http\Controllers;

use App\Exports\GenericExport;
use App\Models\DeviceHistorie;
use App\Models\InventorieDevice;
use Illuminate\Http\Request;
use Exception;
use Inertia\Inertia;
use Maatwebsite\Excel\Facades\Excel;

class DeviceHistoriesController extends Controller
{
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
                // ->orWhereRaw("DATE_FORMAT(created_at, '%e %M, %Y, %l:%i %p') like ?", ["%$search%"]);
            });
        }

        // Ordenación
        if ($request->attribute) {
            $query->orderBy($request->attribute, $request->order);
        } else {
            $query->orderBy('id', 'desc');
        }

        // Paginación
        $histories = $query->paginate(8)->through(function ($item) {
            return [
                'state' => $item->state,
                'id' => $item->id,
                //'device_internal_id' => $item->device_internal_id,
                'comment' => $item->comment,
                'device' => $item->inventorieDevice,
                'user' => $item->user,
                'creator' => $item->creator,
                'date' => \Carbon\Carbon::parse($item->created_at)
                    ->locale('es')
                    ->translatedFormat('j F, Y, g:i a'),
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
            'totalHistoriesCount' => DeviceHistorie::count(),
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
    public function destroy($id)
    {
        try {
            $deviceHistorie = DeviceHistorie::findOrFail($id);
            $deviceHistorie->delete();
            return redirect()->route('historieDevices.index')
                ->with('success', 'El dispositivo ha sido eliminado con éxito');
        } catch (Exception $e) {
            return redirect()->route('historieDevices.index')
                ->with('error', 'Error al intentar eliminar registro');
        }
    }

    public function exportExcel()
    {
        $data = DeviceHistorie::with(['inventorieDevice:id,mac_address', 'user:id,name', 'creator:id,name']);

        $query = $data;

        $headings = [
            'ID',
            'Comment',
            'ID Inventorie Device',
            'Mac Address',
            'ID User',
            'User',
            'ID Creator',
            'Creator',
            'State',
            'Created_at',
            'Date format',
        ];

        $mappingCallback = function ($historie) {
            // dd($historie);
            return [
                $historie->id,
                $historie->comment,
                $historie->inventorieDevice->id ?? '-',
                $historie->inventorieDevice->mac_address ?? '-',
                $historie->user->id ?? '-',
                $historie->user->name ?? '-',
                $historie->creator->id ?? '-',
                $historie->creator->name ?? '-',
                $historie->state ? 'En uso' : 'Disponible',
                $historie->created_at,
                \Carbon\Carbon::parse($historie->created_at)
                    ->locale('es')
                    ->translatedFormat('j F, Y, g:i a'),
            ];
        };
        return Excel::download(new GenericExport($query, $headings, $mappingCallback), 'Hisotrial de dispositivos.xlsx');
    }
}
