<?php

namespace App\Http\Controllers;

use App\Http\Requests\Router\StoreRouterRequest;
use App\Models\Router;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;

class RouterController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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

        $router = Router::created([
            'ip_address' => $validatedData['ip_address'],
            'user' => $validatedData['user'],
            'password' => Hash::make($validatedData['password']),
        ]);

        return redirect()->route('routers')->with('success', 'Router registrado con éxito');
    }

    /**
     * Display the specified resource.
     */
    public function show(Router $router)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Router $router)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Router $router)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Router $router)
    {
        //
    }
}
