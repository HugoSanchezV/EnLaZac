<?php

namespace App\Http\Controllers;

use App\Models\TelegramSetting;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;

class TelegramSettingController extends Controller
{
    //
    public function edit()
    {
        $settings = TelegramSetting::all()->first(); // Recupera todas las configuraciones
        return Inertia::render('Admin/Settings/Telegram/Edit', [
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
            'active' => 'required|between:0,1',
            'api_id' => 'required|string',
            'hash' => 'required|string',
        ]);

        // Crea y guarda la configuración
        $setting = TelegramSetting::create($request->all());

        // Retorna la nueva configuración creada
        return response()->json($setting, 201);
    }

    /**
     * Mostrar una configuración específica.
     */
    public function show($id)
    {
        $setting = TelegramSetting::findOrFail($id); // Busca por ID
        return response()->json($setting); // Devuelve en formato JSON
    }

    /**
     * Actualizar una configuración de PayPal.
     */
    public function update(Request $request)
    {
        $validateData = $request->validate([
            'active' => 'required|between:0,1',
            'api_id' => 'nullable|string',
            'hash' => 'nullable|string',
        ]);


        try {
            $setting = TelegramSetting::first();
            if (!$setting) {
                TelegramSetting::create([
                    'active' => $validateData['active'],
                    'api_id' => $validateData['api_id'],
                    'hash' => $validateData['hash'],
                ]);
            } else {
                $setting->update($request->only([
                    'active',
                    'api_id',
                    'hash',
                ]));
            }

            return Redirect::route('settings.telegram.edit')->with('success', 'Actualizado Con Éxito');
        } catch (Exception $e) {
            return Redirect::route('settings.telegram.edit')->with('error', 'Hubo un error al actualizar credenciales');
        }
    }

    /**
     * Eliminar una configuración de PayPal.
     */
    public function destroy($id)
    {
        $setting = TelegramSetting::findOrFail($id); // Encuentra la configuración
        $setting->delete(); // Elimina la configuración

        // Respuesta con código 204 (No Content)
        return response()->json(null, 204);
    }
}
