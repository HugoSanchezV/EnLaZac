<?php

namespace App\Http\Controllers;

use App\Http\Requests\User\StoreUserRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;

class UserController extends Controller
{
    //
    public function index(Request $request)
    {
        $query = User::query();

        // Excluir administradores
        // dd($request->type);

        if ($request->type !== null && $request->type !== 'todos') {
            $query->where('admin', '=', $request->type);
        }

        $query->where('admin', '!=', 1);
        // Aplicar término de búsqueda
        if ($request->has('q')) {
            $search = $request->input('q');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                    ->orWhere('email', 'like', "%$search%")
                    ->orWhere('alias', 'like', "%$search%")
                    ->orWhere('id', 'like', "%$search%");
                // Puedes agregar más campos si es necesario
            });
        }

        if ($request->order) {
            $query->orderBy($request->order, 'asc');
        } else {
            $query->orderBy('id', 'asc');
        }

        $users = $query->latest()->paginate(8)->through(function ($item) {
            return [
                'id' => $item->id,
                'name' => $item->name,
                'alias' => $item->alias ?? "Sin asignar",
                'email' => $item->email,
                'role' => $item->admin,
            ];
        });

        // Si deseas depurar la salida, puedes usar `dd($users)` aquí

        return Inertia::render('Admin/Users/Usuarios', [
            'users' => $users,
            'pagination' => [
                'links' => $users->links()->elements[0],
                'next_page_url' => $users->nextPageUrl(),
                'prev_page_url' => $users->previousPageUrl(),
                'per_page' => $users->perPage(),
                'total' => $users->total(),
            ],
            'success' => session('success') ?? null,
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

        return redirect()->route('usuarios')->with('success', 'Usuario creado con éxito', 'user');
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);
        return Inertia::render('Admin/Users/Edit', [
            'user' => $user
        ]);
    }

    public function update(UpdateUserRequest $request, $id)
    {
        $user = User::findOrFail($id);

        $validatedData = $request->validated();

        if (!empty($validatedData['password'])) {
            $validatedData['password'] = Hash::make($validatedData['password']);
        } else {
            unset($validatedData['password']);
        }

        $user->update($validatedData);
        return redirect()->route('usuarios')->with('success', 'Usuario Actualizado Con Éxito');
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();
        return Redirect::route('usuarios')->with('success', 'Usuario Eliminado Con Éxito');
    }
}