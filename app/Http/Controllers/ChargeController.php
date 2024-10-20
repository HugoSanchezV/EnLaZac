<?php

namespace App\Http\Controllers;

use App\Http\Requests\Charge\StoreChargeRequest;
use App\Http\Requests\Charge\UpdateChargeRequest;
use App\Models\Charge;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Contract;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;

class ChargeController extends Controller
{
    public function index(Request $request)
    {
        $query = Charge::query();

        //if ($request->type !== null && $request->type !== 'todos') {
        //    $query->where('admin', '=', $request->type);
       // }

        //$query->where('admin', '!=', 1);

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

        if ($request->attribute) {
            $query->orderBy($request->attribute, $request->order);
        } else {
            $query->orderBy('id', 'asc');
        }

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
            'totalChargesCount' => $totalChargesCount 
        ]);
    }

    public function edit($id)
    {
        $charge = Charge::findOrFail($id);
        $contracts = Contract::with('user', 'plan')->get();

        return Inertia::render('Admin/Charges/Edit', [
            'charge' => $charge,
            'contracts' => $contracts,
        ]);
    }
    public function update(UpdateChargeRequest $request, $id)
    {
        $charge = Charge::findOrFail($id);

        $validatedData = $request->validated();
        $charge->update($validatedData);
        return redirect()->route('charges')->with('success', 'Cargo Actualizado Con Éxito');
    }
    public function store_schedule(Charge $request)
    {   
        $charge = Charge::create([
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
        $contracts = Contract::with('user', 'plan')->get();
        
        return Inertia::render(
            'Admin/Charges/Create',
            [
                'contracts' => $contracts,
            ]
        );
    }
    public function store(StoreChargeRequest $request)
    {   
        //dd('HEre');
        $charge = Charge::create([
            'contract_id' => $request->contract_id,
            'description' => $request->description,
            'amount' => $request->amount,
            'paid' => $request->paid,
            'date_paid' => $request->date_paid,
        ]);
        
        return redirect()->route('charges')->with('success', 'El cargo ha sido creado con éxito');
    }
    public function destroy($id)
    {
        $charge = Charge::findOrFail($id);
        $charge->delete();
        return Redirect::route('charge')->with('success', 'Cargo fue Eliminado Con Éxito');
    }
}
