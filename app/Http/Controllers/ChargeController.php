<?php

namespace App\Http\Controllers;

use App\Exports\GenericExport;
use App\Http\Requests\Charge\StoreChargeRequest;
use App\Http\Requests\Charge\UpdateChargeRequest;
use App\Models\Charge;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Contract;
use Illuminate\Support\Facades\Redirect;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Maatwebsite\Excel\Facades\Excel;

use function PHPUnit\Framework\isNull;

class ChargeController extends Controller
{
    public function index(Request $request)
    {
        $query = Charge::query();

        if ($request->has('q')) {
            $search = $request->input('q');
            $query->where(function ($q) use ($search) {
                $q->where('id', 'like', "%$search%")
                    ->orWhere('contract_id', 'like', "%$search%")
                    ->orWhere('description', 'like', "%$search%")
                    ->orWhere('amount', 'like', "%$search%")
                    ->orWhere('paid', 'like', "%$search%")
                    ->orWhere('date_paid', 'like', "%$search%")
                    ->orWhere('created_at', 'like', "%$search%");
                // Puedes agregar más campos si es necesario
            });
        }

        $order = 'asc';
        if ($request->order && in_array(strtolower($request->order), ['asc', 'desc'], true)) {
            $order = strtolower($request->order);
        }
        $query->orderBy(
            $request->attribute ?: 'id',
            $order
        );

        $charges = $query->latest()->paginate(8)->through(function ($item) {
            return [
                'id' => $item->id,
                'contract_id' => $item->contract_id,
                'description' => $item->description,
                'amount' => $item->amount,
                'paid' =>  $item->paid,
                'date_paid' =>  $item->date_paid ?? '',
                'created_at' => $item->created_at->format('Y-m-d H:i:s'),
            ];
        });

        $totalChargesCount = Charge::count();

        return Inertia::render('Admin/Charges/Charges', [
            'charges' => $charges,
            'pagination' => [
                'links' => $charges->links()->elements[0],
                'next_page_url' => $charges->nextPageUrl(),
                'prev_page_url' => $charges->previousPageUrl(),
                'per_page' => $charges->perPage(),
                'total' => $charges->total(),
            ],
            'success' => session('success') ?? null,
            'error' => session('error') ?? null,
            'totalChargesCount' => $totalChargesCount
        ]);
    }

    public function show($id)
    {
        $charge = Charge::with('contract.inventorieDevice.device.user')->findOrFail($id);

        return Inertia::render('Admin/Charges/Show', [
            'charge' => $charge,
        ]);
    }

    public function edit($id)
    {
        try {

            $charge = Charge::findOrFail($id);
            $contracts = Contract::with('inventorieDevice', 'plan')->get();

            return Inertia::render('Admin/Charges/Edit', [
                'charge' => $charge,
                'contracts' => $contracts,
            ]);
        } catch (Exception $e) {
            return redirect()->route('charges')->with('error', 'Hubo un error al obtener la información del registro');
        }
    }
    public function update(UpdateChargeRequest $request, $id)
    {
        try {

            $charge = Charge::findOrFail($id);

            $validatedData = $request->validated();
            $charge->update($validatedData);
            return redirect()->route('charges')->with('success', 'Cargo Actualizado Con Éxito');
        } catch (Exception $e) {
            return redirect()->route('charges')->with('error', 'Error Al Actualizar El Cargo');
        }
    }
    public function updatePaid($id)
    {
        $charge = Charge::findOrFail($id);
        $charge->paid = true;
        $charge->date_paid = Carbon::now();

        $charge->save();
    }
    public function installationCharge($type, $contract_id){
        Charge::create([
            'contract_id'
        ]);
    }
    public function store_schedule(Charge $request)
    {

        Charge::create([
            'contract_id' => $request->contract_id,
            'description' => $request->description,
            'amount' => $request->amount,
            'paid' => $request->paid
        ]);
        // print('Cargo creado');
        // return redirect()->route('')->with('success', 'Ticket creado con éxito');
    }

    public function create()
    {
        $contracts = Contract::with('inventorieDevice.device.user', 'plan')->get();
        
        return Inertia::render(
            'Admin/Charges/Create',
            [
                'contracts' => $contracts,
            ]
        );
    }
    public function store(StoreChargeRequest $request)
    {
        try {

            $validatedData = $request->validated();
            Charge::create([
                'contract_id' => $validatedData['contract_id'],
                'description' => $validatedData['description'],
                'amount' => $validatedData['amount'],
                'paid' => $validatedData['paid'],
                'date_paid' => $validatedData['date_paid'],
            ]);

            return redirect()->route('charges')->with('success', 'El cargo ha sido creado con éxito');
        } catch (Exception $e) {
            return redirect()->route('charges')->with('error', 'Hubo un error al crear el cargo');
        }
    }

    public function destroy(Request $request, $id)
    {
        $data  = [
            "q" => $request->q ?? null,
            "attribute" => $request->attribute ?? null,
            "order" => $request->order ?? null,];
        try {
            $charge = Charge::findOrFail($id);
            $charge->delete();
            return Redirect::route('charges', $data)->with('success', 'Cargo fue Eliminado Con Éxito');
        } catch (Exception $e) {
            return Redirect::route('charges', $data)->with('error', 'Error al cargar el registro');
        }
    }

    public function paidInstallation($id)
    {
        $charge = Charge::findOrFail($id);

        $charge->update([
            'paid' => true,
            'date_paid' => now(),]);

    }
    public function paidCharge($id)
    {
        $charge = Charge::findOrFail($id);
        $controller = new PaymentSanctionController;

        if(!is_null($charge)){
            if($charge->description === "recargo-mes"){
               // $contract = Contract::findOrFail($charge->contract_id);
                $controller->fromPayment($charge->contract_id);
            }


            $charge->update([
                'paid' => true,
                'date_paid' => now(),]);
        }



        // $charges = Charge::where('contract_id', $id)
        // ->whereNotIn('description', ['cambio-domicilio', 'instalacion-inicial'])
        // ->whereIn('paid',false)
        // ->get();
        // $controller = new PaymentSanctionController;
        // $count = 0;
        // //3 registros
        // foreach($charges as $charge){
        //     Log::info("Cargos de contrato: ".$charge->description);
        //     if($charge->description === "recargo-mes"){
        //         $count++;
        //     }
        //     $charge->update([
        //         'paid' => true,
        //         'date_paid' => now(),]);
            
        // }
        // if($count > 0){
        //     Log::info("SANCION A ACTIVARSE");
        //     $controller->fromPayment($id);
        // }
    }

    public function exportExcel()
    {
        try {

            $query = Charge::with(['contract.plan']);   
            //dd($query);
           // throw new Exception("hola");
            $headings = [
                'ID',
                'CONTRATO ID',
                'CONTRATO NOMBRE',
                'PLAN ID',
                'PLAN NOMBRE',
                'DESCRIPCION',
                'CANTIDAD',
                '¿PAGADO?',
                'FECHA DE PAGO',
            ];

            $mappingCallback = function ($charge) {
                return [
                    'id' => $charge->id,
                    'contract_id' => $charge->contract ? $charge->contract->id : null,
                    'contract' => $charge->contract ? $charge->contract->address : null, // Ajusta según el campo correcto
                    'plan_id' => $charge->contract && $charge->contract->plan ? $charge->contract->plan->id : null,
                    'plan_name' => $charge->contract && $charge->contract->plan ? $charge->contract->plan->name : null,
                    'description' => $charge->description,
                    'amount' => $charge->amount,
                    'paid' => $charge->paid ? "SI" : "NO",
                    'date_paid' => $charge->date_paid,
                ];
            };

            return Excel::download(new GenericExport($query, $headings, $mappingCallback), 'cargos.xlsx');
        } catch (Exception $e) {
            return Redirect::back()->with('error', 'Error al cargar el registro');
        }
    }
}
