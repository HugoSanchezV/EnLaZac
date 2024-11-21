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
        $settings = MercadoPagoSetting::first();
        return Inertia::render('Admin/Settings/MercadoPago/Edit', [
            'settings' => $settings
        ]);
    }

    /**
     * Guardar o actualizar la configuración de MercadoPago.
     */
    public function storeOrUpdate(Request $request)
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
            $fields = $request->only([
                'mode',
                'sandbox_client_id',
                'sandbox_client_secret',
                'live_client_id',
                'live_client_secret',
                'currency',
            ]);

            $setting = MercadoPagoSetting::first();
            if ($setting) {
                $setting->update($fields);
                $message = 'Configuración actualizada con éxito.';
            } else {
                MercadoPagoSetting::create($fields);
                $message = 'Configuración creada con éxito.';
            }

            return Redirect::route('settings.mercadopago.edit')->with('success', $message);
        } catch (Exception $e) {
            Log::error('Error al guardar configuración de MercadoPago', [
                'error' => $e->getMessage(),
                'input' => $request->all(),
            ]);
            return Redirect::route('settings.mercadopago.edit')->with('error', 'Hubo un error al guardar las credenciales.');
        }
    }

    /**
     * Eliminar la configuración de MercadoPago.
     */
    public function destroy($id)
    {
        try {
            $setting = MercadoPagoSetting::findOrFail($id);
            $setting->delete();
            return response()->json(['message' => 'Configuración eliminada con éxito.'], 200);
        } catch (Exception $e) {
            Log::error('Error al eliminar configuración de MercadoPago', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Hubo un error al eliminar la configuración.'], 500);
        }
    }
}
