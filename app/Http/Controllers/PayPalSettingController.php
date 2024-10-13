<?php

namespace App\Http\Controllers;

use App\Models\PaypalAccount;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;

class PayPalSettingController extends Controller
{

    public function edit()
    {
        $settings = PaypalAccount::all()->first(); // Recupera todas las configuraciones
        return Inertia::render('Admin/Settings/PayPal/Edit', [
            'settings' => $settings
        ]); // Devuelve en formato JSON
    }

    /**
     * Guardar una nueva configuración de PayPal.
     */
    public function store(Request $request)
    {
        // Valida los datos que llegan desde la petición
        $request->validate([
            'mode' => 'required|in:sandbox,live',
            'sandbox_client_id' => 'required|string',
            'sandbox_client_secret' => 'required|string',
            'live_client_id' => 'nullable|string',
            'live_client_secret' => 'nullable|string',
            'currency' => 'required|string',
        ]);

        // Crea y guarda la configuración
        $setting = PayPalSetting::create($request->all());

        // Retorna la nueva configuración creada
        return response()->json($setting, 201);
    }

    /**
     * Mostrar una configuración específica.
     */
    public function show($id)
    {
        $setting = PayPalSetting::findOrFail($id); // Busca por ID
        return response()->json($setting); // Devuelve en formato JSON
    }

    /**
     * Actualizar una configuración de PayPal.
     */
    public function update(Request $request)
    {
        $request->validate([
            'live_client_id' => 'nullable|string',
            'live_client_secret' => 'nullable|string'
        ]);


        try {
            $setting = PaypalAccount::first();
            if (!$setting) {
                PaypalAccount::create([
                    'mode' => 'live',
                    'live_client_id' => $request->live_client_id,
                    'live_client_secret' => $request->live_client_secret,
                    'currency' => 'USD'
                ]);
            } else {
                $setting->update($request->only(['live_client_id', 'live_client_secret']));
            }

            return Redirect::route('settings.paypal.edit')->with('success', 'Usuario Actualizado Con Éxito');
        } catch (Exception $e) {
            dd($e->getMessage());
            return Redirect::route('settings.paypal.edit')->with('error', 'Hubo un error al actualizar credenciales');
        }
    }

    /**
     * Eliminar una configuración de PayPal.
     */
    public function destroy($id)
    {
        $setting = PayPalSetting::findOrFail($id); // Encuentra la configuración
        $setting->delete(); // Elimina la configuración

        // Respuesta con código 204 (No Content)
        return response()->json(null, 204);
    }
}
