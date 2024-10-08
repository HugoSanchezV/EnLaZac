<?php

namespace App\Http\Controllers;

use App\Models\Ping;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;

class PingController extends Controller
{
    public function index(Request $request)
    {
        $query = Ping::query();

        if ($request->has('q')) {
            $search = $request->input('q');
            $query->where(function ($q) use ($search) {
                $q->where('id', 'like', "%$search%")
                    ->orWhere('content', 'like', "%$search%")
                    ->orWhere('router_id', 'like', "%$search%")
                    ->orWhere('created_at', 'like', "%$search%");
                // Puedes agregar más campos si es necesario
            });
        }

        if ($request->attribute) {
            $query->orderBy($request->attribute, $request->order);
        } else {
            $query->orderBy('id', 'asc');
        }

        $pings = $query->latest()->paginate(8)->through(function ($item) {
            return [
                'id' => $item->id,
                'router_id' => $item->router->ip_address ?? 'None',
                'content' => $item->content,
                'created_at' => $item->created_at->format('Y-m-d H:i:s'),
            ];
        });

        
        $totalPingsCount = Ping::count();

        return Inertia::render('Admin/Pings/Pings', [
            'pings' => $pings,
            'pagination' => [
                'links' => $pings->links()->elements[0],
                'next_page_url' => $pings->nextPageUrl(),
                'prev_page_url' => $pings->previousPageUrl(),
                'per_page' => $pings->perPage(),
                'total' => $pings->total(),
            ],
            'success' => session('success') ?? null,
            'totalPingsCount' => $totalPingsCount 
        ]);
    }

    public function store(Ping $request)
    {   
        $ping = Ping::create([
            'content' => $request->content,
            'router_id' => $request->router_id,
        ]);
        //return redirect()->route('tickets')->with('success', 'Ticket creado con éxito');   
    }
    public function destroy($id)
    {
        $ping = Ping::findOrFail($id);
        $ping->delete();
        return Redirect::route('pings')->with('success', 'Ping Eliminado Con Éxito');
    }
}
