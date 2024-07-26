<?php

namespace App\Http\Controllers;

use App\Http\Requests\User\StoreUserRequest;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;

class UserController extends Controller
{
    //
    public function index()
    {
        $users = User::where('admin', '!=', 1)
            ->latest()
            ->paginate(1)
            ->through(function ($item) {
                return [
                    'id' => $item->id,
                    'name' => $item->name,
                    'alias' => $item->alias === null? "Sin asignar": $item->alias,
                    'email' => $item->email,
                    'role' => $item->admin,
                ];
            });

        return Inertia::render('Admin/Users/Usuarios', [
            'user' => Auth::user(),
            'users' => $users,
            'pagination' => [
                'links' => $users->links()->elements[0], // Proporciona los enlaces de paginación
                'next_page_url' => $users->nextPageUrl(),
                'prev_page_url' => $users->previousPageUrl(),
                'per_page' => $users->perPage(),
                'total' => $users->total(),
            ],
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/Users/Create', [
            'user' => Auth::user(),
        ]);
    }

    public function store(StoreUserRequest $request)
    {
        $validatedData = $request->validated();

        $user = User::create([
            'name' => $validatedData['name'],
            'alias' => $validatedData['alias'],
            'email' => $validatedData['email'],
            'password' => Hash::make($validatedData['password']),
            'admin' => $validatedData['admin'],
        ]);

        return redirect()->route('usuarios')->with(['success' => 'Usuario creado con éxito', 'user' => $user], 201);
    }
}
