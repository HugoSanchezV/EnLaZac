<?php

namespace App\Http\Controllers;

use App\Http\Requests\Router\StoreRouterRequest;
use App\Http\Requests\Router\UpdateRouterRequest;
use App\Models\Router;
use App\Models\RouterosAPI;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;

class RouterController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {

        $query = Router::query();

        // if ($request->type !== null && $request->type !== 'todos') {
        //      $query->where('admin', '=', $request->type);
        // }

        // $query->where('admin', '!=', 1);

        if ($request->has('q')) {
            $search = $request->input('q');
            $query->where(function ($q) use ($search) {
                $q->where('user', 'like', "%$search%")
                    ->orWhere('id', 'like', "%$search%")
                    ->orWhere('ip_address', 'like', "%$search%");
            });
        }

        if ($request->order) {
            $query->orderBy($request->order, 'asc');
        } else {
            $query->orderBy('id', 'asc');
        }

        $routers = $query->latest()->paginate(8)->through(function ($item) {
            return [
                'sync' => $item->sync,
                'id' => $item->id,
                'user' => $item->user,
                'ip_address' => $item->ip_address,
            ];
        });

        $totalRoutersCount = Router::count();

        return Inertia::render('Admin/Routers/Index', [
            'routers' => $routers,
            'pagination' => [
                'links' => $routers->links()->elements[0],
                'next_page_url' => $routers->nextPageUrl(),
                'prev_page_url' => $routers->previousPageUrl(),
                'per_page' => $routers->perPage(),
                'total' => $routers->total(),
            ],
            'success' => session('success') ?? null,
            'totalRoutersCount' => $totalRoutersCount,
        ]);

        return Inertia::render('Admin/Routers/Index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
        return Inertia::render('Admin/Routers/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRouterRequest $request)
    {
        //
        $validatedData = $request->validated();

        $router = Router::create([
            'ip_address' => $validatedData['ip_address'],
            'user' => $validatedData['user'],
            'password' => Crypt::encrypt($validatedData['password']),
        ]);


        return redirect()->route('routers')->with('success', 'Router registrado con éxito');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $router = Router::findOrFail($id);
        return Inertia::render('Admin/Routers/Edit', [
            'router' => $router
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRouterRequest $request, $id)
    {
        $router = Router::findOrFail($id);
        $validatedData = $request->validated();

        if (!empty($validatedData['password'])) {
            $validatedData['password'] = Hash::make($validatedData['password']);
        } else {
            unset($validatedData['password']);
        }

        $router->update($validatedData);
        return redirect()->route('routers')->with('success', 'Router Actualizado Con Éxito');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $router = Router::findOrFail($id);
        $router->delete();
        return Redirect::route('routers')->with('success', 'Router Eliminado Con Éxito');
    }

    public function sync($id)
    {
        $router = Router::findOrFail($id);

        $ip = $router->ip_address;
        $user = $router->user;
        $password = Crypt::decrypt($router->password);

        $API = new RouterosAPI();

        
        $data = [];

        $API->debug(false);

        if ($API->connect($ip, $user, $password)) {
            $identitas = $API->comm('/ip/firewall/address-list/print');
dd($identitas);
            $data = [
                'identitas' => $identitas,
            ];
        } else {
            return Redirect::route('routers')->with('error', 'No se ha podido sincronizar el Router, intentalo más tarde');
        }

        return Redirect::route('routers')->with('success', 'Router Sincronizado con Éxito');
    }
}
