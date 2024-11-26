<?php

namespace App\Http\Controllers;

use App\Models\SMSSetting;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Illuminate\Support\Facades\Log;

class SMSSettingController extends Controller
{
    /**
     * Mostrar el formulario de edición de configuraciones de SMS.
     */
    //dd("hiska");
    /**public function edit()
    {
        $settings = SMSSetting::first(); // Recupera la primera configuración
       
        return Inertia::render('Admin/Settings/SMS/Edit', [
            'settings' => $settings,
        ]);
    }
    */
    public function edit()
{
    try {
       
        $settings = SMSSetting::first();

        return Inertia::render('Admin/Settings/SMS/Edit', [
            'settings' => $settings,
        ]);
    } catch (\Exception $e) {
        
        dd($e->getMessage());
    }
}


    /**
     * Guardar una nueva configuración de SMS.
     */
    public function store(Request $request)
    {
        // Validar los datos de la solicitud
        $request->validate([
            'provider' => 'required|string', // Ejemplo: twilio, nexmo, etc.
            'account_sid' => 'required|string',
            'auth_token' => 'required|string',
            'phone_number' => 'required|string',
        ]);

        try {
            $setting = SMSSetting::create($request->all());
            return response()->json($setting, 201); // Configuración creada con éxito
        } catch (Exception $e) {
            Log::error('Error al guardar configuración de SMS', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'No se pudo guardar la configuración.'], 500);
        }
    }

    /**
     * Mostrar una configuración específica.
     */
    public function show($id)
    {
        $setting = SMSSetting::findOrFail($id);
        return response()->json($setting); // Devuelve la configuración en formato JSON
    }

    /**
     * Actualizar una configuración de SMS.
     */
    public function update(Request $request)
    {
        $request->validate([
            'provider' => 'required|string',
            'account_sid' => 'nullable|string',
            'auth_token' => 'nullable|string',
            'phone_number' => 'nullable|string',
        ]);

        try {
            $setting = SMSSetting::first();
            if (!$setting) {
                SMSSetting::create($request->only([
                    'provider',
                    'account_sid',
                    'auth_token',
                    'phone_number',
                ]));
            } else {
                $setting->update($request->only([
                    'provider',
                    'account_sid',
                    'auth_token',
                    'phone_number',
                ]));
            }

            return Redirect::route('settings.sms.edit')->with('success', 'Configuración actualizada con éxito.');
        } catch (Exception $e) {
            Log::error('Error al actualizar configuración de SMS', ['error' => $e->getMessage()]);
            return Redirect::route('settings.sms.edit')->with('error', 'Hubo un error al actualizar la configuración.');
        }
    }

    /**
     * Eliminar una configuración de SMS.
     */
    public function destroy($id)
    {
        try {
            $setting = SMSSetting::findOrFail($id);
            $setting->delete();
            return response()->json(null, 204); // Configuración eliminada con éxito
        } catch (Exception $e) {
            Log::error('Error al eliminar configuración de SMS', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Hubo un error al eliminar la configuración.'], 500);
        }
    }
}
