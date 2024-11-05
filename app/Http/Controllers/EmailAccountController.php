<?php

namespace App\Http\Controllers;

use App\Http\Requests\Email\UpdateEmailRequest;
use Illuminate\Http\Request;
use App\Models\EmailAccount;
use Inertia\Inertia;
use Exception;
use Illuminate\Support\Facades\Redirect;

class EmailAccountController extends Controller
{
    public function edit()
    {
        $account = EmailAccount::all()->first();

        return Inertia::render('Admin/Settings/Email/Edit', [
            'account' => $account,
            'success' => session('success') ?? null,
            'error' => session('error') ?? null,
        ]);
    }
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|max:50',
            'email' => [
                'requierd',
                'email',
                'max:200',
            ]
        ]);

        // Crea y guarda la configuración
        $account = EmailAccount::create($request->all());

        // Retorna la nueva configuración creada
        return response()->json($account, 201);
    }

    public function update(UpdateEmailRequest $request)
    {
        try {
            $account = EmailAccount::first();
            if (!$account) {
                $account = EmailAccount::create([
                    'name' =>$request->name,
                    'email' =>$request->email,
                ]);
            } else {
                $account->update($request->only(['name', 'email']));
            }

            return Redirect::route('settings.email.edit')->with('success', 'Email Actualizado Con Éxito');
        } catch (Exception $e) {
            return Redirect::route('settings.email.edit')->with('error', 'Hubo un error al actualizar las credenciales');
        }
    }
}
