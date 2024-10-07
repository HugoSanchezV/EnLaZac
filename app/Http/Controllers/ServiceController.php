<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ServiceController extends Controller
{
    public function index(Request $request)
    {
        $query = Service::query();

        if ($request->has('q')) {
            $search = $request->input('q');
            $query->where(function ($q) use ($search) {
                $q->where('id', 'like', "%$search%")
                    ->orWhere('contract_id', 'like', "%$search%")
                    ->orWhere('charge', 'like', "%$search%")
                    ->orWhere('mounths', 'like', "%$search%")
                    ->orWhere('total', 'like', "%$search%");
                // Puedes agregar más campos si es necesario
            });
        }

        if ($request->attribute) {
            $query->orderBy($request->attribute, $request->order);
        } else {
            $query->orderBy('id', 'asc');
        }

        $services = $query->latest()->paginate(8)->through(function ($item) {
            return [
                'id' => $item->id,
                'contract_id' => $item->contract_id,
                'charge' => $item->charge,
                'mounths' => $item->mounths,
                'total' =>  $item->total,
            ];
        });

        
        $totalServicesCount = Service::count();

        return Inertia::render('Coordi/Tickets/Tickets', [
            'services' => $services,
            'pagination' => [
                'links' => $services->links()->elements[0],
                'next_page_url' => $services->nextPageUrl(),
                'prev_page_url' => $services->previousPageUrl(),
                'per_page' => $services->perPage(),
                'total' => $services->total(),
            ],
            'success' => session('success') ?? null,
            'totalServicesCount' => $totalServicesCount 
        ]);
    }
}
