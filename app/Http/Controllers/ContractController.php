<?php

namespace App\Http\Controllers;

use App\Models\Contract;
use Illuminate\Http\Request;
use App\Http\Requests\Contract\StoreContractRequest;
use App\Http\Requests\Contract\UpdateContractRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;

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
                    ->orWhere('user_id', 'like', "%$search%")
                    ->orWhere('plan_id', 'like', "%$search%")
                    ->orWhere('start_date', 'like', "%$search%")
                    ->orWhere('end_date', 'like', "%$search%")
                    ->orWhere('active', 'like', "%$search%")
                    ->orWhere('address', 'like', "%$search%");
                // Puedes agregar más campos si es necesario
            });
        }

        if ($request->order) {
            $query->orderBy($request->order, 'asc');
        } else {
            $query->orderBy('id', 'asc');
        }

        $contract = $query->latest()->paginate(8)->through(function ($item) {
            return [
                'id' => $item->id,
                'user_id' => $item->user_id,
                'plan_id' => $item->plan_id,
                'start_date' => $item->start_date,
                'end_date' => $item->end_date,
                'active' => $item->active,
                'address' => $item->address,
                'geolocation' => $item -> geolocation ?[
                    'latitude' => $item->geolocation['latitude'] ?? null,
                    'longitude' => $item->geolocation['longitude'] ?? null,
                ] : null,

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
        return Inertia::render('Coordi/Contracts/Create');
    }

    public function store(StoreContractRequest $request)
    {   
        $validatedData = $request->validated();
        $contract = Contract::create([
            'user_id' => $validatedData['user_id'],
            'plan_id' => $validatedData['plan_id'],
            'start_date' => $validatedData['start_date'],
            'end_date' => $validatedData['end_date'],
            'active' => $validatedData['active'],
            'address' => $validatedData['address'],
            'geolocation' => $validatedData['geolocation'],
        ]);

        return redirect()->route('contracts')->with('success', 'Contrato creado con éxito');

        
    }

    public function edit($id)
    {
        $contract = Contract::findOrFail($id);

        return Inertia::render('Coordi/Contracts/Edit', [
            'contract' => $contract,
        ]);
    }

    
    public function update(UpdateContractRequest $request, $id)
    {
        $contract = Contract::findOrFail($id);

        $validatedData = $request->validated();
        $contract->update($validatedData);
        return redirect()->route('contracts')->with('success', 'Contrato Actualizado Con Éxito');
    }
    
    public function destroy($id)
    {
        $contract = Contract::findOrFail($id);
        $contract->delete();
        return Redirect::route('contracts')->with('success', 'Contrato Eliminado Con Éxito');
    }
   
}
