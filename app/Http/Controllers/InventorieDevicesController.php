<?php

namespace App\Http\Controllers;

use App\Exports\GenericExport;
use App\Http\Requests\InventorieDevice\StoreInventorieDeviceRequest;
use App\Http\Requests\InventorieDevice\UpdateInventorieDeviceRequest;
use App\Imports\InventorieDeviceImport;
use App\Models\DeviceHistorie;
use App\Models\InventorieDevice;
use App\Services\InventorieDeviceService;
use Carbon\Carbon;
use Dotenv\Exception\ValidationException;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Maatwebsite\Excel\Facades\Excel;

use function PHPUnit\Framework\isNull;

class InventorieDevicesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = InventorieDevice::query();

        if ($request->has('q')) {
            $search = $request->input('q');
            $query->where(function ($q) use ($search) {
                $q->where('mac_address', 'like', "%$search%")
                    ->orWhere('description', 'like', "%$search%")
                    ->orWhere('brand', 'like', "%$search%");
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

        $devices = $query->latest()->paginate(8)->through(function ($item) {
            return [
                'state' => $item->state,
                'id' => $item->id,
                'mac_address' => $item->mac_address,
                'description' => $item->description,
                'brand' => $item->brand,
            ];
        });

        $totalDevicesCount = InventorieDevice::count();

        return Inertia::render('Admin/InventorieDevices/Index', [
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

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Admin/InventorieDevices/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreInventorieDeviceRequest $request)
    {
        $validatedData = $request->validated();

        try {
            DB::transaction(function () use ($validatedData) {
                $device = InventorieDevice::create([
                    'mac_address' => $validatedData['mac_address'],
                    'description' => $validatedData['description'],
                    'brand' => $validatedData['brand'],
                ]);
                DeviceHistorie::create([
                    'state' => 0,
                    'comment' => 'Se ha registrado un nuevo dispositivo',
                    'device_id' => $device->id,
                    'user_id' => null,
                    'creator_id' => Auth::id(),
                ]);
            });
        } catch (\Exception $e) {
            return redirect()->route('inventorie.devices.index')->with('error', 'Hubo un error al intentar realizar el registro');
        }
        return redirect()->route('inventorie.devices.index')->with('success', 'El dispositivo ha sido agregado con éxito');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $inventorieDevices = InventorieDevice::findOrFail($id);

        return Inertia::render('Admin/InventorieDevices/Show', [
            'intentorieDevices' => $inventorieDevices,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $device = InventorieDevice::findOrFail($id);

        return Inertia::render('Admin/InventorieDevices/Edit', [
            'device' => $device
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateInventorieDeviceRequest $request, string $id)
    {
        try {
            $device = InventorieDevice::findOrFail($id);
            $validateData = $request->validated();

            $comment = InventorieDeviceService::getChanges($device, $validateData);

            $device->update($validateData);

            DeviceHistorie::create([
                'state' => $device->state,
                'comment' => $comment,
                'inv_device_id' => $device->id,
                'user_id' => $device->user_id ?? null,
                'creator_id' => Auth::id(),
            ]);
            return redirect()->route('inventorie.devices.index')->with('success', 'Dispositivo Actualizado Con Éxito');
        } catch (\Exception $e) {
            return redirect()->route('inventorie.devices.index')->with('error', 'Hubo un error al intentar modificar el registro');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, $id)
    {
        try {
            DB::transaction(function () use ($id) {
                $device = InventorieDevice::findOrFail($id);

                DeviceHistorie::create([
                    'state' => null,
                    'comment' => 'Se ha Eliminado el Dispositivo',
                    'device_id' => $id,
                    'user_id' => $device->user_id ?? null,
                    'creator_id' => Auth::id(),
                ]);

                $device->delete();
            });
        } catch (\Exception $e) {
            return redirect()->route('inventorie.devices.index',  [
                "q" => $request->q,
                "attribute" => $request->attribute,
                "order" => $request->order,
            ])->with('error', 'Hubo un error al intentar eliminar el registro');
        }

        return redirect()->route('inventorie.devices.index',  [
            "q" => $request->q,
            "attribute" => $request->attribute,
            "order" => $request->order,
        ])
            ->with('success', 'Dispositivo Eliminado Con Éxito');
    }

    public static function changeStateDevice($id, $state = 0)
    {
        $device = InventorieDevice::findOrFail($id);
        $device->state = $state;
        $device->save();
    }

    public function exportExcel()
    {
        $query = InventorieDevice::query();

        $headings = [
            'Estado',
            'ID',
            'Mac Address',
            'Descripción',
            'Marca',
            'Agregado',
            'Modificado',
        ];

        $mappingCallback = function ($device) {
            return [
                $device->state ? 'En uso' : 'Disponible',
                $device->id,
                $device->mac_address,
                $device->description,
                $device->brand,
                $device->created_at,
                $device->update_at,
            ];
        };

        $timestamp = Carbon::now()->format('Y-m-d_H-i-s');

        return Excel::download(new GenericExport($query, $headings, $mappingCallback), 'Inventario de Dispositivos ' . $timestamp . '.xlsx');
    }

    public function importExcel(Request $request)
    {
        // dd('Inventario de dispositivos');
        try {
            $file = $request->excel;
            Excel::import(new InventorieDeviceImport, $file);
            return Redirect::route('inventorie.devices.index')->with('success', 'Archivo Importado Con Éxito ');
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
            return Redirect::route('inventorie.devices.index')->with('error', 'Error al Importar ' . $e->getMessage());
        }
    }
}
