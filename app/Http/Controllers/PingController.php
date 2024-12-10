<?php

namespace App\Http\Controllers;

use App\Models\Ping;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;

use function PHPUnit\Framework\isNull;

class PingController extends Controller
{
    public function index(Request $request, $url = 'Admin/Pings/Pings')
    {
        $query = Ping::query();

        if (Auth::user()->admin === 3) {
            $url = 'Tecnico/Pings/Pings';
        }

        if ($request->has('q')) {
            $search = $request->input('q');
            $query->where(function ($q) use ($search) {
                $q->where('id', 'like', "%$search%")
                    ->orWhere('content', 'like', "%$search%")
                    // ->orWhere('router_id', 'like', "%$search%")
                    ->orWhereHas('router', function ($q) use ($search) {
                        $q->where('ip_address', 'like', "%$search%");
                    })
                    ->orWhere('created_at', 'like', "%$search%");
                // Puedes agregar más campos si es necesario
            });
        }

        // Ordenación
        $order = 'asc';
        if ($request->order && in_array(strtolower($request->order), ['asc', 'desc'], true)) {
            $order = strtolower($request->order);
        }
        $query->orderBy(
            $request->attribute ?: 'id',
            $order
        );

        $pings = $query->latest()->paginate(8)->through(function ($item) {
            return [
                'id' => $item->id,
                'router_id' => $item->router->ip_address ?? 'None',
                'content' => $item->content,
                'created_at' => $item->created_at->format('Y-m-d H:i:s'),
            ];
        });


        $totalPingsCount = Ping::count();

        return Inertia::render($url, [
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
    public function destroy($id, Request $request)
    {
        $data = [
            "q" => $request->q,
            "attribute" => $request->attribute,
            "order" => $request->order,
        ];
        $path = 'pings';

        if (Auth::user()->admin === 3) {
            $path = 'technical.pings';
        }
        try {
            $ping = Ping::findOrFail($id);
            $ping->delete();
            return Redirect::route($path, $data)->with('success', 'Ping Eliminado Con Éxito');
        } catch (Exception $e) {
            return Redirect::route($path, $data)->with('error', 'Error al cargar el registro');
        }
    }
}
