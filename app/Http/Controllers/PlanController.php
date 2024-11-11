<?php

namespace App\Http\Controllers;

use App\Http\Requests\Plan\StorePlanRequest;
use App\Http\Requests\Plan\UpdatePLanRequest;
use App\Models\Plan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;

class PlanController extends Controller
{
    //
    public function index(Request $request)
    {
        $query = Plan::query();

        if ($request->has('q')) {
            $search = $request->input('q');
            $query->where(function ($q) use ($search) {
                $q->where('id', 'like', "%$search%")
                    ->orWhere('name', 'like', "%$search%")
                    ->orWhere('description', 'like', "%$search%")
                    ->orWhere('price', 'like', "%$search%")
                    ->orWhere('upload_limits->burst_limit', 'like', "%$search%")
                    ->orWhere('upload_limits->burst_threshold', 'like', "%$search%")
                    ->orWhere('upload_limits->burst_time', 'like', "%$search%")
                    ->orWhere('upload_limits->limite_at', 'like', "%$search%")
                    ->orWhere('upload_limits->max_limit', 'like', "%$search%")
                    ->orWhere('download_limits->burst_limit', 'like', "%$search%")
                    ->orWhere('download_limits->burst_threshold', 'like', "%$search%")
                    ->orWhere('download_limits->burst_time', 'like', "%$search%")
                    ->orWhere('download_limits->limite_at', 'like', "%$search%")
                    ->orWhere('download_limits->max_limit', 'like', "%$search%");
                // Puedes agregar más campos si es necesario
            });
        }

        if ($request->order) {
            $query->orderBy($request->order, 'asc');
        } else {
            $query->orderBy('id', 'asc');
        }

        $plans = $query->latest()->paginate(8)->through(function ($item) {
            return [
                'id' => $item->id,
                'name' => $item->name,
                'description' => $item->description,
                'price' => $item->price,
                'burst_limit' => $item->burst_limit ?[
                    'upload_limits' => $item->burst_limit['upload_limits'] ?? null,
                    'download_limits' => $item->burst_limit['download_limits'] ?? null,
                ] : null,

                'burst_threshold' => $item->burst_threshold ?[
                    'upload_limits' => $item->burst_threshold['upload_limits'] ?? null,
                    'download_limits' => $item->burst_threshold['download_limits'] ?? null,
                ] : null,

                'burst_time' => $item->burst_time ?[
                    'upload_limits' => $item->burst_time['upload_limits'] ?? null,
                    'download_limits' => $item->burst_time['download_limits'] ?? null,
                ] : null,

                'limite_at' => $item->limite_at ?[
                    'upload_limits' => $item->limite_at['upload_limits'] ?? null,
                    'download_limits' => $item->limite_at['download_limits'] ?? null,
                ] : null,

                'max_limit' => $item->max_limit ?[
                    'upload_limits' => $item->max_limit['upload_limits'] ?? null,
                    'download_limits' => $item->max_limit['download_limits'] ?? null,
                ] : null,
      
            ];
        });

        
        $totalPlansCount = Plan::count();

        return Inertia::render('Coordi/Plans/Plans', [
            'plans' => $plans,
            'pagination' => [
                'links' => $plans->links()->elements[0],
                'next_page_url' => $plans->nextPageUrl(),
                'prev_page_url' => $plans->previousPageUrl(),
                'per_page' => $plans->perPage(),
                'total' => $plans->total(),
            ],
            'success' => session('success') ?? null,
            'totalPlansCount' => $totalPlansCount 
        ]);
    }
    //Muestra la información del plan de internet y del usuario en específico
    public function show($id)
    {
        $plan = Plan::findOrFail($id);

        return Inertia::render('Coordi/Plans/Show', [
            'plan' => $plan,
        ]);
    }

    public function create()
    {
        return Inertia::render('Coordi/Plans/Create');
    }

    public function store(StorePlanRequest $request)
    {  
        $validatedData = $request->validated();
        Plan::create($validatedData);

        return redirect()->route('plans')->with('success', 'Plan de internet creado con éxito');

        
    }

    public function edit($id)
    {
        $plan = Plan::findOrFail($id);

        return Inertia::render('Coordi/Plans/Edit', [
            'plan' => $plan,

        ]);
    }

    
    public function update(UpdatePlanRequest $request, $id)
    {
        $plan = Plan::findOrFail($id);

        $validatedData = $request->validated();
        $plan->update($validatedData);
        return redirect()->route('plans')->with('success', 'Plan de internet Actualizado Con Éxito');
    }

    public function destroy($id)
    {
        $plan = Plan::findOrFail($id);
        $plan->delete();
        return Redirect::route('plans')->with('success', 'Plan de internet Eliminado Con Éxito');
    }
   
}
