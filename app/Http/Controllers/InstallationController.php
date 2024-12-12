<?php

namespace App\Http\Controllers;

use App\Console\Commands\UpdateContractDate;
use App\Http\Requests\Installation\StoreInstallationRequest;
use App\Http\Requests\Installation\UpdateInstallationRequest;
use App\Models\Contract;
use App\Models\Installation;
use App\Models\InstallationSetting;
use App\Services\ChargeService;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Illuminate\Support\Facades\Redirect;

use function PHPUnit\Framework\isNull;

class InstallationController extends Controller
{
    protected $path = 'Admin';

    public function __construct()
    {
        if (Auth::user()->admin === 2) {
            $this->path = 'Coordi';
        }
    }
    public function index(Request $request)
    {
        $query = Installation::query();

        if ($request->has('q')) {
            $search = $request->input('q');
            $query->where(function ($q) use ($search) {
                $q->where('id', 'like', "%$search%")
                    ->orWhere('contract_id', 'like', "%$search%")
                    ->orWhere('description', 'like', "%$search%")
                    ->orWhere('assigned_date', 'like', "%$search%");
                // Puedes agregar más campos si es necesario
            });
        }

        // Ordenación
        $order = 'asc';
        if ($request->order && isNull($request->order)) {
            $order = $request->order;
        }
        $query->orderBy(
            $request->attribute ?: 'id',
            $order
        );


        $installation = $query->with('contract.inventorieDevice.device.user')->latest()->paginate(8)->through(function ($item) {
            return [
                'id' => $item->id,
                'contract_id' => $item->contract->inventorieDevice->device->user->name,
                'description' => $item->description,
                'assigned_date' => $item->assigned_date,
            ];
        });


        $totalInstallationCount = Installation::count();

        return Inertia::render( $this->path . '/Installation/Installation', [
            'installation' => $installation,
            'pagination' => [
                'links' => $installation->links()->elements[0],
                'next_page_url' => $installation->nextPageUrl(),
                'prev_page_url' => $installation->previousPageUrl(),
                'per_page' => $installation->perPage(),
                'total' => $installation->total(),
            ],
            'success' => session('success') ?? null,
            'error' => session('error') ?? null,
            'totalInstallationCount' => $totalInstallationCount
        ]);
    }


    public function show($id)
    {
        $installation = Installation::with('contract.inventorieDevice.device.user')->findOrFail($id);

        return Inertia::render('Admin/Installation/Show', [
            'installation' => $installation,
        ]);
    }

    public function edit($id)
    {
        $installation = Installation::findOrFail($id);


        // $contractIds = Installation::select('contract_id')->get();
        // $contracts = Contract::with('user')
        // ->whereNotIn('id', $contractIds)
        // ->orWhere('id', $installation->contract_id)
        // ->get();
        $contracts = Contract::with('inventorieDevice.device.user')->get();


        return Inertia::render('Admin/Installation/Edit', [
            'installation' => $installation,
            'contracts' => $contracts
        ]);
    }
    public function update(UpdateInstallationRequest $request, $id)
    {
        try{
          //  $today = Carbon::now();
            $installation = Installation::with('installationSettings', 'contract')->findOrFail($id);

            $validatedData = $request->validated();
            
            if($this->updateEndDateContract($installation, $validatedData['assigned_date'], $validatedData['description'])){

                $installation->update($validatedData);

                return redirect()->route('installation')->with('success', 'La Instalación fue Actualizada Con Éxito');
            }else{
                return redirect()->route('installation')->with('error', 'Este contrato ya no es posible actualizarlo: Primer pago realizado');
            }
            
        }catch(Exception $e){
            return redirect()->route('installation')->with('error', 'Hubo un error al actualizar el registro');
        }
    }
    public function create()
    {
        $contracts = Contract::with('inventorieDevice.device.user')->get();


        return Inertia::render('Admin/Installation/Create', [
            'contracts' => $contracts
        ]);
    }
    public function store(StoreInstallationRequest $request, $confirmacion)
    {
      //  dd($confirmacion);
        try{
            $validatedData = $request->validated();


            $installation =  Installation::create([
                'contract_id' => $validatedData['contract_id'],
                'description' => $validatedData['description'],
                'assigned_date' => $validatedData['assigned_date'],
            ]);
    
            InstallationSetting::create([
                'installation_id' => $installation->id,
            ]);

            if(($confirmacion == "true"))
            {
                if($installation->description == 2)
                {self::createCharge($installation);}
            }

            $this->storeEndDateContract($installation);
            return redirect()->route('installation')->with('success', 'La Instalación ha sido creado con éxito');

    
        }catch(Exception $e){
            dd($e);
            return redirect()->route('installation')->with('error', 'Error al crear la instalación');
        }
    }

    public function createCharge(Installation $installation)
    {
            $contract = Contract::findOrFail($installation->contract_id);
            $service = new ChargeService();
            $service->creataChargeAddressChange($contract);
       // $controller = new ChargeController();
       // $controller->installationCharge($request->type, $request->contract_id);
    }
    public function destroy($id, Request $request)
    {
        $data = [
            "q" => $request->q,
            "attribute" => $request->attribute,
            "order" => $request->order,
        ];
        try {
            $community = Installation::findOrFail($id);
            $community->delete();
            return Redirect::route('installation', $data)->with('success', 'La Instalación fue Eliminado Con Éxito');
        } catch (Exception  $e) {
            return Redirect::route('installation', $data)->with('error', 'Error al cargar el registro');
        }
    }

    private function updateEndDateContract(Installation $installation, $newInstallation, $description)
    {
        $controller = new ContractController();

        if($description == "1"){
            $installation = Installation::findOrFail($installation->id)->with('installationSettings');
            //dd($installation->description);

            if($installation->installationSettings){
                return $controller->updateEndDateContract($installation, $newInstallation, $installation->installationSettings->exemption_months);
            }else{
                return $controller->updateEndDateContract($installation, $newInstallation, null );
            }
        }
        return true;
    }
    private function storeEndDateContract(Installation $installation)
    {
        $controller = new ContractController();

        $installation = Installation::with('installationSettings')->findOrFail($installation->id);

     //   dd($installation->description);
        if($installation->description == "1"){
            if($installation->installationSettings){
               
                $controller->storeEndDateContract($installation->contract_id, $installation->assigned_date, $installation->installationSettings->exemption_months);
            }else{
                $controller->storeEndDateContract($installation->contract_id, $installation->assigned_date, null);

            }

        }

    }
}
