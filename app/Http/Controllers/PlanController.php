<?php

namespace App\Http\Controllers;

use App\Http\Requests\Plan\StorePlanRequest;
use App\Http\Requests\Plan\UpdatePLanRequest;
use App\Models\Plan;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;

use function PHPUnit\Framework\isNull;

class PlanController extends Controller
{
    //
    public function index(Request $request)
    {
        $query = Plan::query();

        if ($request->has('q')) {
            $search = $request->input('q');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                    ->orWhere('description', 'like', "%$search%")
                    ->orWhere('price', 'like', "%$search%")
                    ->orWhereRaw("JSON_UNQUOTE(JSON_EXTRACT(burst_limit, '$.upload_limits')) LIKE ?", ["%$search%"])
                    ->orWhereRaw("JSON_UNQUOTE(JSON_EXTRACT(burst_limit, '$.download_limits')) LIKE ?", ["%$search%"])
                    ->orWhereRaw("JSON_UNQUOTE(JSON_EXTRACT(burst_threshold, '$.upload_limits')) LIKE ?", ["%$search%"])
                    ->orWhereRaw("JSON_UNQUOTE(JSON_EXTRACT(burst_threshold, '$.download_limits')) LIKE ?", ["%$search%"])
                    ->orWhereRaw("JSON_UNQUOTE(JSON_EXTRACT(burst_time, '$.upload_limits')) LIKE ?", ["%$search%"])
                    ->orWhereRaw("JSON_UNQUOTE(JSON_EXTRACT(burst_time, '$.download_limits')) LIKE ?", ["%$search%"])
                    ->orWhereRaw("JSON_UNQUOTE(JSON_EXTRACT(limite_at, '$.upload_limits')) LIKE ?", ["%$search%"])
                    ->orWhereRaw("JSON_UNQUOTE(JSON_EXTRACT(limite_at, '$.download_limits')) LIKE ?", ["%$search%"])
                    ->orWhereRaw("JSON_UNQUOTE(JSON_EXTRACT(max_limit, '$.upload_limits')) LIKE ?", ["%$search%"])
                    ->orWhereRaw("JSON_UNQUOTE(JSON_EXTRACT(max_limit, '$.download_limits')) LIKE ?", ["%$search%"]);
                // Puedes agregar más campos si es necesario
            });
        }

        $order = 'asc';
        if ($request->order && isNull($request->order)) {
            $order = $request->order;
        }
        $query->orderBy(
            $request->attribute ?: 'id',
            $order
        );

        $plans = $query->latest()->paginate(8)->through(function ($item) {
            return [
                'id' => $item->id,
                'name' => $item->name,
                'description' => $item->description,
                'price' => $item->price,
                'burst_limit' => $this->extractJsonLimits($item->burst_limit),
                'burst_threshold' => $this->extractJsonLimits($item->burst_threshold),
                'burst_time' => $this->extractJsonLimits($item->burst_time),
                'limite_at' => $this->extractJsonLimits($item->limite_at),
                'max_limit' => $this->extractJsonLimits($item->max_limit),
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

    private function extractJsonLimits($jsonLimits)
    {
        return $jsonLimits ? [
            'upload_limits' => $jsonLimits['upload_limits'] ?? null,
            'download_limits' => $jsonLimits['download_limits'] ?? null,
        ] : null;
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

    public function destroy(Request $request, $id)
    {
        $data = [
            "q" => $request->q ?? null,
            "attribute" => $request->attribute ?? null,
            "order" => $request->order ?? null,
        ];
        try {
            $plan = Plan::findOrFail($id);
            $plan->delete();
            return Redirect::route('plans', $data)->with('success', 'Plan de internet Eliminado Con Éxito');
        } catch (Exception $e) {
            return Redirect::route('plans', $data)->with('errror', 'Error al cargar el registro');
        }
    }
}
