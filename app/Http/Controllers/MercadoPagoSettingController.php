<?php

namespace App\Http\Controllers;

use App\Models\MercadoPagoSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Exception;
use Illuminate\Support\Facades\Log;

class MercadoPagoSettingController extends Controller
{
    /**
     * Mostrar el formulario de edición de configuraciones de MercadoPago.
     */
    public function edit()
    {
        $settings = MercadoPagoSetting::first(); // Recupera la primera configuración
        return Inertia::render('Admin/Settings/MercadoPago/Edit', [
            'settings' => $settings
        ]);
    }

    /**
     * Guardar una nueva configuración de MercadoPago.
     */
    public function store(Request $request)
    {
        // Validar los datos de la solicitud
        $request->validate([
            'mode' => 'required|in:sandbox,live',
            'sandbox_client_id' => 'required|string',
            'sandbox_client_secret' => 'required|string',
            'live_client_id' => 'nullable|string',
            'live_client_secret' => 'nullable|string',
            'currency' => 'required|string',
        ]);

        // Crear y guardar la configuración
        $setting = MercadoPagoSetting::create($request->all());

        // Retornar la nueva configuración creada
        return response()->json($setting, 201);
    }

    /**
     * Mostrar una configuración específica.
     */
    public function show($id)
    {
        $setting = MercadoPagoSetting::findOrFail($id);
        return response()->json($setting);
    }

    /**
     * Actualizar una configuración de MercadoPago.
     */
    public function update(Request $request)
    {
        $request->validate([
            'mode' => 'required|in:sandbox,live',
            'sandbox_client_id' => 'required|string',
            'sandbox_client_secret' => 'required|string',
            'live_client_id' => 'nullable|string',
            'live_client_secret' => 'nullable|string',
            'currency' => 'required|string',
        ]);

        try {
            $setting = MercadoPagoSetting::first();
            if (!$setting) {
                // Si no existe ninguna configuración, crear una nueva
                MercadoPagoSetting::create([
                    'mode' => $request->mode,
                    'sandbox_client_id' => $request->sandbox_client_id,
                    'sandbox_client_secret' => $request->sandbox_client_secret,
                    'live_client_id' => $request->live_client_id,
                    'live_client_secret' => $request->live_client_secret,
                    'currency' => $request->currency,
                ]);
            } else {
                // Si existe, actualizar la configuración existente
                $setting->update($request->only([
                    'mode',
                    'sandbox_client_id',
                    'sandbox_client_secret',
                    'live_client_id',
                    'live_client_secret',
                    'currency',
                ]));
            }

            return Redirect::route('settings.mercadopago.edit')->with('success', 'Configuración Actualizada Con Éxito');
        } catch (Exception $e) {
            Log::error('Error al actualizar configuración de MercadoPago', ['message' => $e->getMessage()]);
            return Redirect::route('settings.mercadopago.edit')->with('error', 'Hubo un error al actualizar las credenciales');
        }
    }

    /**
     * Eliminar una configuración de MercadoPago.
     */
    public function destroy($id)
    {
        $setting = MercadoPagoSetting::findOrFail($id);
        $setting->delete();

        return response()->json(null, 204);
    }
}
