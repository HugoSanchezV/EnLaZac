<?php

namespace App\Http\Controllers;

use App\Http\Requests\RuralCommunity\StoreRuralCommunityRequest;
use App\Http\Requests\RuralCommunity\UpdateRuralCommunityContractRequest;
use App\Http\Requests\RuralCommunity\UpdateRuralCommunityRequest;
use App\Models\Contract;
use App\Models\RuralCommunity;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Redirect;

class RuralCommunityController extends Controller
{
    public function index(Request $request)
    {
        $query = RuralCommunity::query();

        if ($request->has('q')) {
            $search = $request->input('q');
            $query->where(function ($q) use ($search) {
                $q->where('id', 'like', "%$search%")
                    ->orWhere('name', 'like', "%$search%")
                    ->orWhere('installation_cost', 'like', "%$search%");
                // Puedes agregar más campos si es necesario
            });
        }

        if ($request->attribute) {
            $query->orderBy($request->attribute, $request->order);
        } else {
            $query->orderBy('id', 'asc');
        }

        $community = $query->latest()->paginate(8)->through(function ($item) {
            return [
                'id' => $item->id,
                'name' => $item->name,
                'installation_cost' => $item->installation_cost,
            ];
        });

        
        $totalCommunityCount = RuralCommunity::count();

        return Inertia::render('Admin/RuralCommunity/RuralCommunity', [
            'community' => $community,
            'pagination' => [
                'links' => $community->links()->elements[0],
                'next_page_url' => $community->nextPageUrl(),
                'prev_page_url' => $community->previousPageUrl(),
                'per_page' => $community->perPage(),
                'total' => $community->total(),
            ],
            'success' => session('success') ?? null,
            'totalCommunityCount' => $totalCommunityCount 
        ]);
    }

    public function edit($id)
    {
        $community = RuralCommunity::findOrFail($id);
        
        return Inertia::render('Admin/RuralCommunity/Edit', [
            'community' => $community
        ]);
    }
    public function update(UpdateRuralCommunityRequest $request, $id)
    {
        $community = RuralCommunity::findOrFail($id);

        $validatedData = $request->validated();
        $community->update($validatedData);
        return redirect()->route('rural-community')->with('success', 'La Comunidad fue Actualizada Con Éxito');
    }
    public function create()
    {
     
        return Inertia::render('Admin/RuralCommunity/Create');
    }
    public function store(StoreRuralCommunityRequest $request)
    {   
        $validatedData = $request->validated();
        RuralCommunity::create([
            'name' => $validatedData ['name'],
            'installation_cost' => $validatedData['installation_cost'],
        ]);
        
        return redirect()->route('rural-community')->with('success', 'La Comunidad ha sido creado con éxito');
    }
    
    public function destroy($id)
    {
        $community = RuralCommunity::findOrFail($id);
        $community->delete();
        return Redirect::route('rural-community')->with('success', 'La Comunidad fue Eliminado Con Éxito');
    }
}
