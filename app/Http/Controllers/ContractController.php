<?php

namespace App\Http\Controllers;

use App\Exports\GenericExport;
use App\Models\Contract;
use App\Models\User;
use App\Models\Plan;
use Illuminate\Http\Request;
use App\Http\Requests\Contract\StoreContractRequest;
use App\Http\Requests\Contract\UpdateContractRequest;
use App\Models\Charge;
use App\Models\CutOffDay;
use App\Models\Device;
use App\Models\InventorieDevice;
use App\Models\RuralCommunity;
use App\Services\RuralCommunityService;
use Carbon\Carbon;

use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Maatwebsite\Excel\Facades\Excel;

use function PHPUnit\Framework\isNull;

class ContractController extends Controller
{
    //
    public function index(Request $request)
    {
        $query = Contract::query();


        if ($request->has('q')) {
            $search = $request->input('q');
            $query->where(function ($q) use ($search) {
                $q->where('id', 'like', "%$search%")
                    ->orWhere('inv_device_id', 'like', "%$search%")
                    ->orWhere('plan_id', 'like', "%$search%")
                    ->orWhere('start_date', 'like', "%$search%")
                    ->orWhere('end_date', 'like', "%$search%")
                    ->orWhere('active', 'like', "%$search%")
                    ->orWhere('address', 'like', "%$search%")
                    ->orWhere('rural_community_id', 'like', "%$search%");
                // Puedes agregar más campos si es necesario
            });
        }

        if ($request->attribute) {
            $query->orderBy($request->attribute, $request->order);
        } else {
            $query->orderBy('id', 'asc');
        }

        $contract = $query->with('inventorieDevice.device.user', 'ruralCommunity')->latest()->paginate(8)->through(function ($item) {
            return [
                'id' => $item->id,
                'inv_device_id' => $item->inventorieDevice->mac_address ?? 'Sin asignar',
                'user_id' => $item->inventorieDevice->device->user->name ?? 'Sin asignar',
                'plan_id' => $item->plan->name ?? 'Sin asignar',
                'rural_community_id' => $item->ruralCommunity->name ?? 'Sin asignar',
                'start_date' => $item->start_date,
                'end_date' => $item->end_date,
                'active' => $item->active,
                'address' => $item->address,

            ];
        });



        $totalContractsCount = Contract::count();

        return Inertia::render('Coordi/Contracts/Contracts', [
            'contracts' => $contract,
            'pagination' => [
                'links' => $contract->links()->elements[0],
                'next_page_url' => $contract->nextPageUrl(),
                'prev_page_url' => $contract->previousPageUrl(),
                'per_page' => $contract->perPage(),
                'total' => $contract->total(),
            ],
            'success' => session('success') ?? null,
            'error' => session('error') ?? null,
            'totalContractsCount' => $totalContractsCount
        ]);
    }

    public function index_remainig(Request $request)
    {
        $query = Contract::query();

        $days = intval($request->days) ?? 5;

        // dd($days);
        // Filtro para contratos que faltan 5 días o menos para terminar
        // if ($request->has('days_remaining') && $request->input('days_remaining') == 5) {
        $today = Carbon::now()->toDateString();
        $targetDate = Carbon::now()->addDays($days)->toDateString();
        $query->whereBetween('end_date', [$today, $targetDate]);
        // }

        // Filtro de búsqueda
        if ($request->has('q')) {
            $search = $request->input('q');
            $query->where(function ($q) use ($search) {
                $q->where('id', 'like', "%$search%")
                    ->orWhereHas('user', function ($userQuery) use ($search) {
                        $userQuery->where('name', 'like', "%$search%");
                    })
                    ->orWhereHas('plan', function ($planQuery) use ($search) {
                        $planQuery->where('name', 'like', "%$search%");
                    })
                    ->orWhere('start_date', 'like', "%$search%")
                    ->orWhere('end_date', 'like', "%$search%")
                    ->orWhere('active', 'like', "%$search%")
                    ->orWhere('address', 'like', "%$search%")
                    ->orWhereHas('ruralCommunity', function ($communityQuery) use ($search) {
                        $communityQuery->where('name', 'like', "%$search%");
                    });
            });
        }

        // Ordenamiento
        $order = $request->order && !is_null($request->order) ? $request->order : 'asc';
        $query->orderBy(
            $request->attribute ?: 'id',
            $order
        );

        $contract = $query->with('device.device.user', 'ruralCommunity')->latest()->paginate(8)->through(function ($item) {
            return [
                'id' => $item->id,
                'user_id' => $item->device->device->user->name ?? 'Sin asignar',
                'plan_id' => $item->plan->name ?? 'Sin asignar',
                'rural_community_id' => $item->ruralCommunity->name ?? 'Sin asignar',
                'start_date' => $item->start_date,
                'end_date' => $item->end_date,
                'active' => $item->active,
                'address' => $item->address,
            ];
        });

        $totalContractsCount = Contract::count();

        return Inertia::render('Coordi/Contracts/Remainding/Contracts', [
            'contracts' => $contract,
            'pagination' => [
                'links' => $contract->links()->elements[0],
                'next_page_url' => $contract->nextPageUrl(),
                'prev_page_url' => $contract->previousPageUrl(),
                'per_page' => $contract->perPage(),
                'total' => $contract->total(),
            ],
            'success' => session('success') ?? null,
            'totalContractsCount' => $totalContractsCount
        ]);
    }

    //Muestra la información del contrato y del usuario en específico
    public function show($id)
    {
        $contract = Contract::with('user', 'router')->findOrFail($id);

        return Inertia::render('Coordi/Contracts/Show', [
            'contract' => $contract,
        ]);
    }

    public function create()
    {
        $community = RuralCommunity::all();
        $devices = Device::select('id', 'address')->whereNotNull('user_id')->get();
    
        $plans = Plan::select('id', 'name')->get();
        return Inertia::render(
            'Coordi/Contracts/Create',
            [
                'devices' => $devices,
                'plans' => $plans,
                'community' => $community,
            ]
        );
    }

    public function create2()
    {
        try{
            $community = RuralCommunity::all();

            $devices_used = Device::with('inventorieDevice')->whereNotNull('device_id')->whereNotNull('user_id')->get();

            $inv_devices = $devices_used->pluck('inventorieDevice');

            $device_no_contract = Contract::all();

            $available_devices = [];

            if($device_no_contract->count() !== 0){

                foreach ($inv_devices as $device) {
                    // Verificar si el dispositivo no está en la lista de device_no_contract
                    $exists = $device_no_contract->contains('inv_device_id', $device->id);
                    
                    if (!$exists) {
                        $available_devices[] = $device;
                    }
                }
            }else{
            // dd("QQui");
                foreach ($inv_devices as $device) {
                    // Filtrar la colección de contratos para ver si el inv_device_id coincide con el id del dispositivo
                
                    $available_devices [] = $device;
                }
            }
            $plans = Plan::select('id', 'name')->get();
            return Inertia::render(
                'Coordi/Contracts/Create',
                [
                    'devices' => $available_devices,
                    'plans' => $plans,
                    'community' => $community,
                ]
            );
        }catch(Exception $e)
        {
            dd($e);
            return redirect()->route('contracts')->with('error', 'Hubo un error al obtener los registros');

        }

        
    }

    public function store(StoreContractRequest $request)
    {
        try{
            $cutOffDay = CutOffDay::first()->day;
            
            //dd('LLega aqui');
            $validatedData = $request->validated();

            $contract = Contract::create([
                'inv_device_id' => $validatedData['inv_device_id'],
                'plan_id' => $validatedData['plan_id'],
                'start_date' => $validatedData['start_date']."-".$cutOffDay,
                'end_date' => $validatedData['end_date']."-".$cutOffDay,
                'active' => $validatedData['active'],
                'address' => $validatedData['address'],
                'rural_community_id' => $validatedData['rural_community_id'],
                'geolocation' => $validatedData['geolocation'],
            ]);
    
            self::createCharge($contract);
            //     RuralCommunityService::update($id, $request->community);
    
            return redirect()->route('contracts')->with('success', 'Contrato creado con éxito');
        }catch(Exception $e){
            dd($e);
            return redirect()->route('contracts')->with('error', 'Hubo un error al crear el contrato');
        }
    }
    private function createCharge($contract)
    {
        $controller = new ChargeController();
        $cargo = new Charge();
        $community = RuralCommunity::findOrFail($contract->rural_community_id);

        $cargo->contract_id = $contract->id;
        $cargo->description = "Cargo de instalación";
        $cargo->amount = $community->installation_cost;
        $cargo->paid = false;

        $controller->store_schedule($cargo);
    }



    public function edit($id)
    {
        try{
            $contract = Contract::with('inventorieDevice','ruralCommunity')->findOrFail($id);
            $contract->start_date = substr($contract->start_date, 0, -3);
            $contract->end_date = substr($contract->end_date, 0, -3);

            $community = RuralCommunity::all();

            $devices_used = Device::with('inventorieDevice')->whereNotNull('device_id')->whereNotNull('user_id')->get();

            $inv_devices = $devices_used->pluck('inventorieDevice');

            $device_no_contract = Contract::all();

            $available_devices = [];

            if($device_no_contract->count() !== 0){

                foreach ($inv_devices as $device) {
                    // Verificar si el dispositivo no está en la lista de device_no_contract
                    $exists = $device_no_contract->contains('inv_device_id', $device->id);
                    
                    if (!$exists) {
                        $available_devices[] = $device;
                    }
                }
               // dd();
               //dd($contract->inventorieDevice);
                $current_device = $contract->inventorieDevice;
                //dd("ds");
                if ($current_device && !collect($available_devices)->contains('id', $current_device->id)) {
                    $available_devices[] = $current_device;
                }
            }else{
            // dd("QQui");
                foreach ($inv_devices as $device) {
                    // Filtrar la colección de contratos para ver si el inv_device_id coincide con el id del dispositivo
                
                    $available_devices [] = $device;
                }
                $current_device = $contract->pluck('inventorieDevice');
                if ($current_device && !collect($available_devices)->contains('id', $current_device->id)) {
                    $available_devices[] = $current_device;
                }
            }
            $plans = Plan::select('id', 'name')->get();
            return Inertia::render(
                'Coordi/Contracts/Edit',
                [
                    'contract'=> $contract,
                    'devices' => $available_devices,
                    'plans' => $plans,
                    'community' => $community,
                ]
            );
        }catch(Exception $e)
        {
            dd($e);
            return redirect()->route('contracts')->with('error', 'Hubo un error al obtener los registros');

        }
    }


    public function update(UpdateContractRequest $request, $id)
    {
        $cutOffDay = CutOffDay::first()->day;
        $contract = Contract::findOrFail($id);

        $validatedData = $request->validated();
        $contract->update([
            'inv_device_id' => $validatedData['inv_device_id'],
                'plan_id' => $validatedData['plan_id'],
                'start_date' => $validatedData['start_date']."-".$cutOffDay,
                'end_date' => $validatedData['end_date']."-".$cutOffDay,
                'active' => $validatedData['active'],
                'address' => $validatedData['address'],
                'rural_community_id' => $validatedData['rural_community_id'],
                'geolocation' => $validatedData['geolocation'],]
        );
        return redirect()->route('contracts')->with('success', 'Contrato Actualizado Con Éxito');
    }

    public function updateMonths($months, $id)
    {
        try {
            $contract = Contract::findOrFail($id);
            $today = Carbon::today();

            //if($today->day >  ) 
            // Convertir `end_date` a una instancia de Carbon para manipular la fecha
            $endDate = Carbon::parse($contract->end_date);

            // Sumar los meses a la fecha de finalización
            $contract->end_date = $endDate->addMonths(intval($months));

            // Guardar los cambios
            $contract->save();
        } catch (Exception $e) {
            Log::info("Error en updateMonths " . $e->getMessage());
            return throw new Exception($e->getMessage());
        }
    }

    public function destroy(Request $request, $id)
    {
        $data  = [
            "q" => $request->q ?? null,
            "attribute" => $request->attribute ?? null,
            "order" => $request->order ?? null,
        ];
        try {
            $contract = Contract::findOrFail($id);
            $contract->delete();
            return Redirect::route('contracts', $data)->with('success', 'Contrato Eliminado Con Éxito');
        } catch (Exception $e) {
            return Redirect::route('contracts', $data)->with('error', 'Error al cargar el registro'.$e);
        }
    }

    public function getContracts($today)
    {
        return Contract::with('installations')->where('end_date', '<=', $today)->get();
    }
    public function exportExcel()
    {
        $query = Contract::with(['user', 'plan']);

        $headings = [
            'ID',
            'cliente id',
            'cliente',
            'Plan',
            'Fecha incio',
            'Fecha Fin',
            'Estado',
            'Dirección',
        ];

        $mappingCallback = function ($contract) {
            return [
                'id' => $contract->id,
                'cliente id' => $contract->user->id ?? 'None',
                'cliente' => $contract->user->name ?? 'None',
                'plan' => $contract->plan->name ?? 'None',
                'fecha incio' => $contract->start_date,
                'fecha Fin' => $contract->end_date,
                'estado' => $contract->active ? 'Activo' : 'Inactivo',
                'dirección' => $contract->address,
            ];
        };

        return Excel::download(new GenericExport($query, $headings, $mappingCallback), 'contratos.xlsx');
    }

    public function extendEndDate(Request $request, $id)
    {
        // Validar la cantidad de días
        $request->validate([
            'days' => 'required|integer|min:1',
        ]);

        // Buscar el contrato por su ID
        $contract = Contract::find($id);

        // Verificar que el contrato exista
        if (!$contract) {
            return Redirect::route('reaming.contracts')->with('error', 'Error a cargar el registro');
            // return response()->json(['error' => 'Contrato no encontrado'], 404);
        }

        // Sumar los días a la fecha de finalización actual
        $newEndDate = Carbon::parse($contract->end_date)->addDays($request->input('days'));

        // Actualizar la fecha en el contrato
        $contract->end_date = $newEndDate;
        $contract->save();

        // return response()->json([
        //     'message' => 'Fecha de finalización extendida exitosamente',
        //     'new_end_date' => $newEndDate->toDateString(),
        // ]);
        return Redirect::route('reaming.contracts', [
            'days' => $request->daysFilter,
            'q' => $request->q,
            'order' => $request->order,
            'attribute' => $request->attribute,
        ])->with('success', 'Fecha de finalización extendida exitosamente');
    }
}
