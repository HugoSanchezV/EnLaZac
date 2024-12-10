<?php

namespace App\Http\Controllers;

use App\Http\Requests\RuralCommunity\StoreRuralCommunityRequest;
use App\Http\Requests\RuralCommunity\UpdateRuralCommunityContractRequest;
use App\Http\Requests\RuralCommunity\UpdateRuralCommunityRequest;
use App\Models\Contract;
use App\Models\RuralCommunity;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Illuminate\Support\Facades\Redirect;

use function PHPUnit\Framework\isNull;

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

        $order = 'asc';
        if ($request->order && in_array(strtolower($request->order), ['asc', 'desc'], true)) {
            $order = strtolower($request->order);
        }
        $query->orderBy(
            $request->attribute ?: 'id',
            $order
        );

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
            'name' => $validatedData['name'],
            'installation_cost' => $validatedData['installation_cost'],
        ]);

        return redirect()->route('rural-community')->with('success', 'La Comunidad ha sido creado con éxito');
    }

    public function destroy(Request $request, $id)
    {
        $data  = [
            "q" => $request->q ?? null,
            "attribute" => $request->attribute ?? null,
            "order" => $request->order ?? null,
        ];

        try {
            $community = RuralCommunity::findOrFail($id);
            $community->delete();
            return Redirect::route('rural-community', $data)->with('success', 'La Comunidad fue Eliminado Con Éxito');
        } catch (Exception $e) {
            Log::info("Error al eliminar comunidad " . $e->getMessage());
            return Redirect::route('rural-community', $data)->with('error', 'Error al cargar el registro');
        }
    }

    public function show(string $id)
    {
        $ruralcommunity = RuralCommunity::findOrFail($id);
        return Inertia::render('Admin/RuralCommunity/Show', [
            'ruralcommunity' => $ruralcommunity,
        ]);
    }
}
