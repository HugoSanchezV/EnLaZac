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
use App\Models\RuralCommunity;
use App\Services\RuralCommunityService;
use Carbon\Carbon;

use Exception;
use Illuminate\Support\Facades\Auth;
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
                    // ->orWhere('user_id', 'like', "%$search%")
                    ->orWhereHas('user', function ($userQuery) use ($search) {
                        $userQuery->where('name', 'like', "%$search%");
                    })
                    // ->orWhere('plan_id', 'like', "%$search%")
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
                // ->orWhere('rural_community_id', 'like', "%$search%");
            });
        }

        // if ($request->attribute) {
        //     $query->orderBy($request->attribute, $request->order);
        // } else {
        //     $query->orderBy('id', 'asc');
        // }
        
        $order = 'asc';
        if ($request->order && isNull($request->order)) {
            $order = $request->order;
        }
        $query->orderBy(
            $request->attribute ?: 'id',
            $order
        );


        $contract = $query->with('user', 'ruralCommunity')->latest()->paginate(8)->through(function ($item) {
            return [
                'id' => $item->id,
                'user_id' => $item->user->name ?? 'Sin asignar',
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
            'totalContractsCount' => $totalContractsCount
        ]);
    }
    //Muestra la información del contrato y del usuario en específico
    public function show($id)
    {
        $contract = Contract::findOrFail($id);

        return Inertia::render('Coordi/Contracts/Show', [
            'contract' => $contract,
        ]);
    }

    public function create()
    {
        $community = RuralCommunity::all();

        $users = User::select('id', 'name')->where('admin', '=', '0')->get();
        $plans = Plan::select('id', 'name')->get();
        return Inertia::render(
            'Coordi/Contracts/Create',
            [
                'users' => $users,
                'plans' => $plans,
                'community' => $community,
            ]
        );
    }

    public function store(StoreContractRequest $request)
    {

        //dd('LLega aqui');
        $validatedData = $request->validated();
        $contract = Contract::create([
            'user_id' => $validatedData['user_id'],
            'plan_id' => $validatedData['plan_id'],
            'start_date' => $validatedData['start_date'],
            'end_date' => $validatedData['end_date'],
            'active' => $validatedData['active'],
            'address' => $validatedData['address'],
            'rural_community_id' => $validatedData['rural_community_id'],
            'geolocation' => $validatedData['geolocation'],
        ]);

        self::createCharge($contract);
   //     RuralCommunityService::update($id, $request->community);
        
        return redirect()->route('contracts')->with('success', 'Contrato creado con éxito');
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
        $community = RuralCommunity::all();

        $contract = Contract::with('ruralCommunity')->findOrFail($id);
        $users = User::select('id', 'name')->where('admin', '=', '0')->get();
        $plans = Plan::select('id', 'name')->get();

        return Inertia::render('Coordi/Contracts/Edit', [
            'contract' => $contract,
            'users' => $users,
            'plans' => $plans,
            'community' => $community,

        ]);
    }


    public function update(UpdateContractRequest $request, $id)
    {
        // dd("aqui");
        $contract = Contract::findOrFail($id);

        $validatedData = $request->validated();
        $contract->update($validatedData);
        return redirect()->route('contracts')->with('success', 'Contrato Actualizado Con Éxito');
    }

    public function updateMonths($months, $id)
    {
        $contract = Contract::findOrFail($id);

        // Convertir `end_date` a una instancia de Carbon para manipular la fecha
        $endDate = Carbon::parse($contract->end_date);

        // Sumar los meses a la fecha de finalización
        $contract->end_date = $endDate->addMonths($months);

        // Guardar los cambios
        $contract->save();
    }

    public function destroy(Request $request, $id)
    {
        try {
            $contract = Contract::findOrFail($id);
            $contract->delete();
            return Redirect::route('contracts', $request->query())->with('success', 'Contrato Eliminado Con Éxito');
        } catch (Exception $e) {
            return Redirect::route('contracts', $request->query())->with('error', 'Error al cargar el registro');
        }
    }

    public function getContracts($today){
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
}
