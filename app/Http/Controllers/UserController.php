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
        return Inertia::render('Admin/Users/Usuarios', [
            'user' => Auth::user(),
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
