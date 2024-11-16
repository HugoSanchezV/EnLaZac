<?php

namespace App\Http\Controllers;

use App\Http\Requests\PreRegister\StorePreRegisterRequest;
use App\Http\Requests\PreRegister\UpdatePreRegisterRequest;
use App\Models\PreRegisterUser;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class PreRegisterUserController extends Controller
{
    //
    public function index(Request $request)
    {
        $query = PreRegisterUser::query();

        if ($request->has('q')) {
            $search = $request->input('q');
            $query->where(function ($q) use ($search) {
                $q->where('id', 'like', "%$search%")
                    ->orWhere('phone', 'like', "%$search%");
            });
        }

        // Ordenación
        if ($request->attribute) {
            $query->orderBy($request->attribute, $request->order);
        } else {
            $query->orderBy('id', 'desc');
        }

        // Paginación
        $phones = $query->paginate(8)->through(function ($item) {
            return [
                'id' => $item->id,
                'comment' => $item->phone,
            ];
        });

        return Inertia::render('Admin/PreRegister/Index', [
            'phones' => $phones,
            'pagination' => [
                'links' => $phones->links()->elements[0],
                'next_page_url' => $phones->nextPageUrl(),
                'prev_page_url' => $phones->previousPageUrl(),
                'per_page' => $phones->perPage(),
                'total' => $phones->total(),
            ],
            'success' => session('success') ?? null,
            'error' => session('error') ?? null,
            'warning' => session('warning') ?? null,
            'totalPhonesCount' => PreRegisterUser::count(),
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/PreRegister/Create');
    }

    public function store(StorePreRegisterRequest $request)
    {
        try {
            $validatedData = $request->validated();
            PreRegisterUser::create([
                'phone' => $validatedData['phone'],
            ]);
            return redirect()->route('usuarios.pre.register')->with('success', 'Se ha agrego el número con éxito');
        } catch (Exception $e) {
            return redirect()->route('usuarios.pre.register')->with('success', 'Hubo un error al agregar el número');
        }
    }

    public function edit(PreRegisterUser $register)
    {
        try {
            return Inertia::render('Admin/PreRegister/Edit', [
                "register" => $register->get()->first(),
            ]);
        } catch (Exception $e) {
            return redirect()->route('usuarios.pre.register')->with('error', "Error al cargar el registro");
        }
    }

    public function update(UpdatePreRegisterRequest $request, $id)
    {
        try {
            $validatedData = $request->validated();

            $register = PreRegisterUser::findOrFail($id);
            $register->update([
                'phone' => $validatedData['phone'],
            ]);

            return redirect()->route('usuarios.pre.register')->with('success', 'Se ha actualizado el número con éxito');
        } catch (Exception $e) {
            return redirect()->route('usuarios.pre.register')->with('error', 'Ha sucedido un error con el registro');
        }
    }

    public function destroy(Request $request, $id)
    {
        $data = [
            "q" => $request->q ?? null,
            "attribute" => $request->attribute ?? null,
            "order" => $request->order ?? null,
        ];
        try {
            $register = PreRegisterUser::findOrFail($id);
            $register->delete();
            return redirect()->route('usuarios.pre.register', $data)->with('success', 'Se ha eliminado el número con éxito');
        } catch (Exception $e) {
            dd('ERROR AL ELIMINAR ARCHIVO' . $e->getMessage());
            return redirect()->route('usuarios.pre.register', $data)->with('error', 'Ha sucedido un error con el registro');
        }
    }
}
