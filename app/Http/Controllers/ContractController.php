<?php

namespace App\Http\Controllers;

use App\Exports\GenericExport;
use App\Models\Contract;
use App\Models\Plan;
use Illuminate\Http\Request;
use App\Http\Requests\Contract\StoreContractRequest;
use App\Http\Requests\Contract\UpdateContractRequest;
use App\Models\Charge;
use App\Models\CutOffDay;
use App\Models\Device;
use App\Models\ExemptionPeriod;
use App\Models\ExtendContract;
use App\Models\Installation;
use App\Models\InventorieDevice;
use App\Models\PaymentSanction;
use App\Models\RuralCommunity;
use App\Services\ChargeService;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Maatwebsite\Excel\Facades\Excel;
use Twilio\Rest\Microvisor\V1\DeviceContext;
use function PHPUnit\Framework\isNull;
use function PHPUnit\Framework\throwException;

class ContractController extends Controller
{
    public $path = 'Coordi/Contracts';
    //
    public function __construct()
    {
        if(!is_null(Auth::user()))
        {
            if (Auth::user()->admin === 2) {
                $this->path = 'Coordi/Contracts_Coordi';
            }
            
        }
    }

    public function index(Request $request)
    {
        $query = Contract::query();
        ;
        
        if ($request->has('q')) {
            $search = $request->input('q');
            $query->where(function ($q) use ($search) {
                $q->where('id', 'like', "%$search%")
                    ->orWhere('inv_device_id', 'like', "%$search%")
                    ->orWhereHas('inventorieDevice', function ($q) use ($search) {
                        $q->where('mac_address', 'like', "%$search%");
                    })
                    ->orWhereHas('inventorieDevice.device.user', function ($q) use ($search) {
                        $q->where('name', 'like', "%$search%");
                    })
                    ->orWhere('plan_id', 'like', "%$search%")
                    ->orWhere('start_date', 'like', "%$search%")
                    ->orWhere('end_date', 'like', "%$search%")
                    ->orWhere('active', 'like', "%$search%")
                    ->orWhere('address', 'like', "%$search%")
                    ->orWhere('rural_community_id', 'like', "%$search%");
                // Puedes agregar más campos si es necesario
            });
        }

        if ($request->has('active')) {
            $query->where('active', $request->input('active'));
        }

        if ($request->has('expired') && $request->input('expired') == 'true') {
            $query->where('active', '=', '1')
                  ->where('end_date', '<', Carbon::now());
        }
        // Ordenación
        $order = 'asc';
        if ($request->order && in_array(strtolower($request->order), ['asc', 'desc'], true)) {
            $order = strtolower($request->order);
        }
        $query->orderBy(
            $request->attribute ?: 'id',
            $order
        );
        // if ($request->attribute) {
        //     $query->orderBy($request->attribute, $request->order);
        // } else {
        //     $query->orderBy('id', 'asc');
        // }

        $contract = [];

        $contract = $query->with('inventorieDevice.device.user', 'ruralCommunity', 'paymentSanction')->latest()->paginate(8)->through(function ($item)  use ($request) {

            // if ((Auth::user()->admin === 2) && (isset($request->q) || $request->q === '' || isNull($request->q))) {
            //     return;
            // }

            return [
                'id' => $item->id,
                'inv_device_id' => $item->inventorieDevice->mac_address ?? 'Sin asignar',
                'user_id' => $item->inventorieDevice->device->user->name ?? 'Sin asignar',
                'plan_id' => $item->plan->name ?? 'Sin asignar',
                'rural_community_id' => $item->ruralCommunity->name ?? 'Sin asignar',
                // 'start_date' => $item->start_date,
                'end_date' => $item->end_date,
                'active' => $item->active,
                // 'address' => $item->address,

            ];
        });

        $paymentSanction = Contract::with('paymentSanction')->get();

        //dd($paymentSanction);

        $totalContractsCount = Contract::count();

        return Inertia::render($this->path . '/Contracts', [
            'contracts' => $contract,
            'paymentSanction' => $paymentSanction,
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
                    // ->orWhereHas('user', function ($userQuery) use ($search) {
                    //     $userQuery->where('name', 'like', "%$search%");
                    // })
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

        $contract = $query->with('inventorieDevice.device.user', 'ruralCommunity')->latest()->paginate(8)->through(function ($item) {
            return [
                'id' => $item->id,
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
        // $contract = Contract::with('plan', 'ruralCommunity', 'inventorieDevice')->findOrFail($id);
        // $contract = Contract::select(
        //     'contracts.*',
        //     'plans.*',
        //     'rural_communities.*',
        //     'inventorie_devices.*',
        //     'devices.*',
        //     'users.*'
        // )
        //     ->join('plans', 'contracts.plan_id', '=', 'plans.id') // Join con Plan
        //     ->join('rural_communities', 'contracts.rural_community_id', '=', 'rural_communities.id') // Join con RuralCommunity
        //     ->join('inventorie_devices', 'contracts.inv_device_id', '=', 'inventorie_devices.id') // Join con InventorieDevice
        //     ->leftJoin('devices', 'inventorie_devices.id', '=', 'devices.device_id') // Left join con Device
        //     ->leftJoin('users', 'devices.user_id', '=', 'users.id') // Left join con User
        //     ->where('contracts.id', $id)
        //     ->first();

        $contract = Contract::select(
            'contracts.*',
            'plans.*',
            'rural_communities.*',
            'inventorie_devices.*',
            'devices.*',
            'contracts.id as contract_id',
            'contracts.address as contract_address',
            'plans.name as plan_name',
            'devices.address as device_address',
            'users.id as user_id',
            'users.name as user_name',
            'users.email as user_email'
        )
            ->join('plans', 'contracts.plan_id', '=', 'plans.id') // Join con Plan
            ->join('rural_communities', 'contracts.rural_community_id', '=', 'rural_communities.id') // Join con RuralCommunity
            ->join('inventorie_devices', 'contracts.inv_device_id', '=', 'inventorie_devices.id') // Join con InventorieDevice
            ->leftJoin('devices', 'inventorie_devices.id', '=', 'devices.device_id') // Left join con Device (corrección del campo)
            ->leftJoin('users', 'devices.user_id', '=', 'users.id') // Left join con User
            ->where('contracts.id', $id)
            ->first();

            $mapKey = env('VITE_GOOGLE_MAPS_API_KEY');
          //  dd($mapKey);

        return Inertia::render($this->path . '/Show', [
            'contract' => $contract,
            'mapKey' => $mapKey,
        ]);
    }

    public function create()
    {
        $community = RuralCommunity::all();
        $devices = Device::select('id', 'address')->whereNotNull('user_id')->get();

        $plans = Plan::select('id', 'name')->get();
        $mapKey = env('VITE_GOOGLE_MAPS_API_KEY');
        return Inertia::render(
            'Coordi/Contracts/Create',
            [
                'devices' => $devices,
                'plans' => $plans,
                'community' => $community,
                'mapKey' => $mapKey
            ]
        );
    }

    public function create2()
    {
        try {
            $community = RuralCommunity::all();

            $devices_used = Device::with('inventorieDevice')->whereNotNull('device_id')->whereNotNull('user_id')->get();

            $inv_devices = $devices_used->pluck('inventorieDevice');

            $device_no_contract = Contract::all();

            $available_devices = [];

            if ($device_no_contract->count() !== 0) {

                foreach ($inv_devices as $device) {
                    // Verificar si el dispositivo no está en la lista de device_no_contract
                    $exists = $device_no_contract->contains('inv_device_id', $device->id);

                    if (!$exists) {
                        $available_devices[] = $device;
                    }
                }
            } else {
                // dd("QQui");
                foreach ($inv_devices as $device) {
                    // Filtrar la colección de contratos para ver si el inv_device_id coincide con el id del dispositivo

                    $available_devices[] = $device;
                }
            }
            $plans = Plan::select('id', 'name')->get();
            $mapKey = env('VITE_GOOGLE_MAPS_API_KEY');
            return Inertia::render(
                'Coordi/Contracts/Create',
                [
                    'devices' => $available_devices,
                    'plans' => $plans,
                    'community' => $community,
                    'mapKey' => $mapKey,
                ]
            );
        } catch (Exception $e) {
            return redirect()->route('contracts')->with('error', 'Hubo un error al obtener los registros');
        }
    }

    public function store(StoreContractRequest $request)
    {
        try {
            DB::beginTransaction();
            $cutOffDay = CutOffDay::first()->day;

            //dd('LLega aqui');
            $validatedData = $request->validated();

            $contract = Contract::create([
                'inv_device_id' => $validatedData['inv_device_id'],
                'plan_id' => $validatedData['plan_id'],
                'start_date' => $validatedData['start_date'] . "-" . $cutOffDay,
                'end_date' => $validatedData['end_date'] . "-" . $cutOffDay,
                'active' => $validatedData['active'],
                'address' => $validatedData['address'],
                'rural_community_id' => $validatedData['rural_community_id'],
                'geolocation' => $validatedData['geolocation'],
            ]);

            self::createCharge($contract);
            self::sanction($contract);
            self::extend($contract);


            $plan = Plan::findOrFail($request->plan_id);
            $device = Device::where('device_id', $request->inv_device_id)->first();

            $deviceController = new DevicesController();
            $deviceController->setConsumePlanToDevice($device, $plan);

            DB::commit();
            //     RuralCommunityService::update($id, $request->community);
            return redirect()->route('contracts')->with('success', 'Contrato creado con éxito');
        } catch (Exception $e) {
            DB::rollBack();
            dd($e->getMessage());
            return redirect()->route('contracts')->with('error', 'Hubo un error al crear el contrato');
        }
    }
    private function sanction(Contract $contract)
    {
        $controller = new PaymentSanctionController();

        $controller->store($contract->id);
    }
    private function extend(Contract $contract)
    {
        $controller = new ExtendContractController();

        $controller->store($contract->id);
    }
    private function createCharge(Contract $contract)
    {
        $service = new ChargeService();

        $service->createChargeInstallation($contract);

        // $controller = new ChargeController();
        // $cargo = new Charge();
        // $community = RuralCommunity::findOrFail($contract->rural_community_id);

        // $cargo->contract_id = $contract->id;
        // $cargo->description = "Cargo de instalación";
        // $cargo->amount = $community->installation_cost;
        // $cargo->paid = false;

        // $controller->store_schedule($cargo);
    }



    public function edit($id)
    {
        try {
            $contract = Contract::with('inventorieDevice', 'ruralCommunity')->findOrFail($id);
            $contract->start_date = substr($contract->start_date, 0, -3);
            $contract->end_date = substr($contract->end_date, 0, -3);

            $community = RuralCommunity::all();

            $devices_used = Device::with('inventorieDevice')->whereNotNull('device_id')->whereNotNull('user_id')->get();

            $inv_devices = $devices_used->pluck('inventorieDevice');

            $device_no_contract = Contract::all();

            $available_devices = [];

            if ($device_no_contract->count() !== 0) {

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
            } else {
                // dd("QQui");
                foreach ($inv_devices as $device) {
                    // Filtrar la colección de contratos para ver si el inv_device_id coincide con el id del dispositivo

                    $available_devices[] = $device;
                }
                $current_device = $contract->pluck('inventorieDevice');
                if ($current_device && !collect($available_devices)->contains('id', $current_device->id)) {
                    $available_devices[] = $current_device;
                }
            }
            $mapKey = env('VITE_GOOGLE_MAPS_API_KEY');
            $plans = Plan::select('id', 'name')->get();
            return Inertia::render(
                $this->path . '/Edit',
                [
                    'contract' => $contract,
                    'devices' => $available_devices,
                    'plans' => $plans,
                    'community' => $community,
                    'mapKey' => $mapKey,
                ]
            );
        } catch (Exception $e) {
            dd($e);
            return redirect()->route('contracts')->with('error', 'Hubo un error al obtener los registros');
        }
    }


    public function update(UpdateContractRequest $request, $id)
    {
        try {
            DB::transaction(function () use ($request, $id) {
                $cutOffDay = CutOffDay::first()->day;
                $contract = Contract::findOrFail($id);

                $validatedData = $request->validated();

                if ($contract->plan_id !== $validatedData['plan_id']) {
                    $plan = Plan::findOrFail($validatedData['plan_id']);
                    $device = Device::where('device_id', $validatedData['inv_device_id'])->get();

                    $deviceController = new DevicesController();
                    $deviceController->setConsumePlanToDevice($device[0], $plan);
                }

                
                $contract->update(
                    [
                        'inv_device_id' => $validatedData['inv_device_id'],
                        'plan_id' => $validatedData['plan_id'],
                        'start_date' => $validatedData['start_date'] . "-" . $cutOffDay,
                        'end_date' => $validatedData['end_date'] . "-" . $cutOffDay,
                        'active' => $validatedData['active'],
                        'address' => $validatedData['address'],
                        'rural_community_id' => $validatedData['rural_community_id'],
                        'geolocation' => $validatedData['geolocation'],
                    ]
                );
            });
            return redirect()->route('contracts')->with('success', 'Contrato Actualizado Con Éxito');
        } catch (Exception $e) {
            dd($e->getMessage());
            return redirect()->route('contracts')->with('error', 'Error al cargar el registro');
        }
    }

    public function updateMonths($months, $id)
    {
        try {
            $contract = Contract::findOrFail($id);
            //  $today = Carbon::today();

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

            DB::beginTransaction();
            $contract = Contract::findOrFail($id);

            $device = Device::where('device_id', $contract->inv_device_id)->get();

            $deviceController = new DevicesController();
            $deviceController->removeConsumePlanFromDevice($device[0]);

            $contract->delete();
            DB::commit();
            return Redirect::route('contracts', $data)->with('success', 'Contrato Eliminado Con Éxito');
        } catch (Exception $e) {
            DB::rollBack();
            return Redirect::route('contracts', $data)->with('error', 'Error al cargar el registro' . $e);
        }
    }

    public function getContracts()
    {
        return Contract::with('installations.installationSettings')->where('active', 1)->get();
    }
    public function exportExcel()
    {
        $query = Contract::select(
            'contracts.id as id',
            'contracts.inv_device_id',
            'inventorie_devices.mac_address as address',
            'users.id as cliente_id',
            'users.name as cliente',
            'plans.name as plan',
            'contracts.start_date as fecha_inicio',
            'contracts.end_date as fecha_fin',
            'contracts.active as estado',
            'contracts.address as dirección'
        )
            ->join('plans', 'contracts.plan_id', '=', 'plans.id') // Join con Plan
            ->join('rural_communities', 'contracts.rural_community_id', '=', 'rural_communities.id') // Join con RuralCommunity
            ->join('inventorie_devices', 'contracts.inv_device_id', '=', 'inventorie_devices.id') // Join con InventorieDevice
            ->join('devices', 'inventorie_devices.id', '=', 'devices.device_id') // Join con Device
            ->join('users', 'devices.user_id', '=', 'users.id'); // Join con User

        $headings = [
            'ID',
            'Device ID',
            'MAC',
            'cliente ID',
            'cliente',
            'Plan ID',
            'Plan',
            'Fecha incio',
            'Fecha Fin',
            'Estado',
            'Dirección',
        ];

        $mappingCallback = function ($contract) {
            return [
                'id' => $contract->id,
                'dispositivo id' => $contract->inv_device_id,
                'mac' => $contract->address,
                'cliente id' => $contract->cliente_id ?? 'None',
                'cliente' => $contract->cliente ?? 'None',
                'plan id' => $contract->plan_id ?? 'None',
                'plan' => $contract->plan ?? 'None',
                'fecha incio' => $contract->fecha_inicio,
                'fecha Fin' => $contract->fecha_fin,
                'estado' => $contract->estado ? 'Activo' : 'Inactivo',
                'dirección' => $contract->dirección,
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

        try {
            // Buscar el contrato por su ID
            $contract = Contract::find($id);

            // Verificar que el contrato exista
            if (!$contract) {
                return Redirect::route('reaming.contracts')->with('error', 'Error a cargar el registro');
                // return response()->json(['error' => 'Contrato no encontrado'], 404);
            }
            
            // Sumar los días a la fecha de finalización actual
            $newEndDate = Carbon::parse($contract->end_date)->addDays($request->input('days'));

            //Ingresar registro de la extensión
            $this->addExtendContract($contract, $request->input('days'));

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
        } catch (Exception $e) {
            return Redirect::route('reaming.contracts', [
                'days' => $request->daysFilter,
                'q' => $request->q,
                'order' => $request->order,
                'attribute' => $request->attribute,
            ])->with('success', 'Hubo un error al extender la fecha de finalización');
        }
    }
    private function addExtendContract(Contract $contract, $day)
    {
        $controller = new ExtendContractController();
        $controller->extend($contract->id, $day);
    }

    public function updateContractDate(Installation $installation)
    {
        try {
            // dd("DSDSD");
            $contract = Contract::findOrFail($installation->contract_id);
            $exemptionPeriod = ExemptionPeriod::first();

            $dateInst = Carbon::parse($installation->assigned_date);
            if (!empty($installation->installationSettings) && !empty($installation->installationSettings->exemption_months)) {
                $end = $dateInst->addMonths($installation->installationSettings->exemption_months);

                if (Carbon::parse($contract->end_date)->startOfDay() < $end->startOfDay()) {
                    $contract->end_date = Carbon::parse($contract->end_date)->setMonth($end->month);
                    //->addMonths($installation->installationSettings->exemption_months); ;    
                }
            } else {
                //dd($dateInst->day." | ".$exemptionPeriod->end_date);
                if (($dateInst->day >= $exemptionPeriod->start_day) && ($dateInst->day <= $exemptionPeriod->end_day)) {
                    // dd('entro aca');
                    $end = $dateInst->addMonths($exemptionPeriod->month_next);

                    if (Carbon::parse($contract->end_date)->startOfDay() < $end->startOfDay()) {

                        $contract->end_date = Carbon::parse($contract->end_date)->setMonth($end->month);
                    }
                } else if ($dateInst->day > $exemptionPeriod->end_day) {

                    $end = $dateInst->addMonths($exemptionPeriod->month_after_next);
                    if (Carbon::parse($contract->end_date)->startOfDay() < $end->startOfDay()) {
                        $contract->end_date = Carbon::parse($contract->end_date)->setMonth($end);
                    }
                }
            }
            $contract->save();
        } catch (Exception $e) {
            Log::error($e);
            throwException($e);
        }
    }

    public function updateEndDateContract(Installation $installation, $newInstallation, $config_exemption)
    {
        try {
            $controller = new ServiceVariablesController();
            $currentInstallation =  Carbon::parse($installation->assigned_date)->startOfDay();
            Log::info("5. PINSHI CURRENT: ".$currentInstallation);
            $date = Carbon::parse($newInstallation)->startOfDay();

            // dd();
            $contract = Contract::findOrFail($installation->contract_id);
            $endDate = Carbon::parse($contract->end_date)->startOfDay();
            $exemption = $controller->getExemptionPeriods();
            $cutoffday = $controller->getCutOffDay();

               // Log::info("NO HAY NADA PAPI: ");
            if(!is_null($installation->installationSettings)){

                if(is_null($installation->installationSettings->exemption_months))
                {
                    Log::info("antes NO tenia un config");
                    Log::info("currentInstallation: ".$currentInstallation."  y exemption: ".$exemption);
                    $currentAssigned = $this->checkRange($currentInstallation, $exemption);
                }else{
                    Log::info("antes tenia un config");
                    $currentAssigned = $this->checkRangeConfigExemption($currentInstallation, $config_exemption);
                }
            }else{
              //  dd("En este rango");
                Log::info("tiene puro rango");
                $currentAssigned = $this->checkRange($currentInstallation, $exemption);

            }


           // dd($endDate->isoFormat('YYYY-MM')."  <=   ". $currentAssigned->isoFormat('YYYY-MM'));
            Log::info("COMPARAACIÓN: ".$endDate->isoFormat('YYYY-MM')."  <=   ". $currentAssigned->isoFormat('YYYY-MM'));
            if (!($endDate->isoFormat('YYYY-MM') > $currentAssigned->isoFormat('YYYY-MM'))) {
               // dd("En este rango");
                //dd("entro");
                if(is_null($config_exemption))
                {
                    if ($date->day > $exemption->end_day) {
                        //   dd();
                        $date = $date->addMonths((int)$exemption->month_after_next);
                        if ($contract->end_date < $date) {
    
                            $contract->end_date = $date;
                        }else{
                            Log::info("Cosa prohibida");
                        }
                    } elseif ($date->day >= $exemption->start_day && $date->day <= $exemption->end_day) {
    
                        // dd();
                        $date = $date->addMonths((int)$exemption->month_next);
                        //if ($contract->end_date < $date) {
    
                            $contract->end_date = $date;
                       // }
                          //  Log::info("AQUI CALLOODSA[o");
                        
                    } else {
                        $contract->end_date = $date;
                    }
                }else{
                    //dd($config_exemption);
                    $date = Carbon::parse($installation->assigned_date)->addMonths((int)$config_exemption);
                    Log::info("EL DATE: ".$date);
                    $contract->end_date = $currentInstallation;
                }
                Log::info("Este es el contract Guardao: ".$contract);
                $contract->end_date = Carbon::parse($contract->end_date)->setDay((int)$cutoffday);

                //dd($contract->end_date);
                $contract->save();
            } else {
                Log::info("NO Pasó este pedo de más: ".$currentInstallation);
                //dd("NOOOOOOOOOOOO");
                return false;
            }

            return true;
        } catch (Exception $e) {
            dd($e->getMessage());
            return false;
        }
    }

    private function checkRangeConfigExemption(Carbon $date, $config_exemption){
        return $date->addMonths((int)$config_exemption);
    }   
    private function checkRange(Carbon $date, ExemptionPeriod $exemption)
    {
        if ($date->day > $exemption->end_day) {
           // dd("LE TOCO ACA");
            return $date->addMonths((int)$exemption->month_after_next);
        } else if ($date->day >= $exemption->start_day && $date->day <= $exemption->end_day) {
          //  dd("LE TOCO ACA");
            return $date->addMonths((int)$exemption->month_next);
        }
        return $date;
    }
    public function storeEndDateContract($id, $assigned_date, $setting_month)
    {
        try {
            $controller = new ServiceVariablesController();
            $date = Carbon::parse($assigned_date)->startOfDay();

            // dd();
            $contract = Contract::findOrFail($id);
            $exemption = $controller->getExemptionPeriods();
            $cutoffday = $controller->getCutOffDay();
            //dd();

            if (is_null($setting_month)) {

                if ($date->day > $exemption->end_day) {
                    //   dd();
                    $date = $date->addMonths((int)$exemption->month_after_next);
                    if ($contract->end_date < $date) {

                        $contract->end_date = $date;
                    }
                } elseif ($date->day >= $exemption->start_day && $date->day <= $exemption->end_day) {

                    // dd();
                    $date = $date->addMonths((int)$exemption->month_next);
                    if ($contract->end_date < $date) {

                        $contract->end_date = $date;
                    }
                } else {
                    $contract->end_date = $date;
                }

                $contract->end_date = Carbon::parse($contract->end_date)->setDay((int)$cutoffday);
            } else {
                $date = $date->addMonths((int)$setting_month);

                if ($contract->end_date < $date) {

                    $contract->end_date = $date;
                }

                $contract->end_date = Carbon::parse($contract->end_date)->setDay((int)$cutoffday);
            }
            //dd($contract->end_date);
            $contract->save();
        } catch (Exception $e) {
            dd($e->getMessage());
        }
    }

    public function removeDateInstallationSettings($contract_id, $exemption_months){
        try{
            $contract = Contract::findOrFail($contract_id);
            $contract->end_date = Carbon::parse($contract->end_date)->subMonths((int)$exemption_months);
            
            $contract->save();
        }catch(Exception $e){
            Log::error($e->getMessage());
        }
    }
}
