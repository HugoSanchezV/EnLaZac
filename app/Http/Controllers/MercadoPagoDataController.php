<?php

namespace App\Http\Controllers;

use App\Models\MercadoPagoSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Exception; // Importar la clase Exception

class MercadoPagoDataController extends Controller
{
    /**
     * Mostrar una lista de todas las configuraciones de MercadoPago.
     */
    public function index()
    {
        try {
            $settings = MercadoPagoSetting::all();
            return response()->json([
                'success' => true,
                'data' => $settings,
            ], 200);
        } catch (Exception $e) { // Ahora Exception está correctamente referenciada
            Log::error('Error al obtener las configuraciones de MercadoPago', ['message' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'No se pudieron obtener las configuraciones.',
            ], 500);
        }
    }

    /**
     * Almacenar una nueva configuración de MercadoPago en la base de datos.
     */
    public function store(Request $request)
    {
        // Validar los datos de la solicitud
        $validator = Validator::make($request->all(), [
            'mode' => 'required|in:sandbox,live',
            'sandbox_client_id' => 'required|string',
            'sandbox_client_secret' => 'required|string',
            'live_client_id' => 'nullable|string',
            'live_client_secret' => 'nullable|string',
            'currency' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            // Crear y guardar la configuración
            $setting = MercadoPagoSetting::create($request->all());

            return response()->json([
                'success' => true,
                'data' => $setting,
                'message' => 'Configuración creada exitosamente.',
            ], 201);
        } catch (Exception $e) {
            Log::error('Error al crear configuración de MercadoPago', ['message' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'No se pudo crear la configuración.',
            ], 500);
        }
    }

    /**
     * Mostrar una configuración específica.
     */
    public function show($id)
    {
        try {
            $setting = MercadoPagoSetting::findOrFail($id);
            return response()->json([
                'success' => true,
                'data' => $setting,
            ], 200);
        } catch (Exception $e) {
            Log::error('Error al obtener la configuración de MercadoPago', ['message' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Configuración no encontrada.',
            ], 404);
        }
    }

    /**
     * Actualizar una configuración específica en la base de datos.
     */
    public function update(Request $request, $id)
    {
        // Validar los datos de la solicitud
        $validator = Validator::make($request->all(), [
            'mode' => 'required|in:sandbox,live',
            'sandbox_client_id' => 'required|string',
            'sandbox_client_secret' => 'required|string',
            'live_client_id' => 'nullable|string',
            'live_client_secret' => 'nullable|string',
            'currency' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $setting = MercadoPagoSetting::findOrFail($id);
            $setting->update($request->all());

            return response()->json([
                'success' => true,
                'data' => $setting,
                'message' => 'Configuración actualizada exitosamente.',
            ], 200);
        } catch (Exception $e) {
            Log::error('Error al actualizar la configuración de MercadoPago', ['message' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'No se pudo actualizar la configuración.',
            ], 500);
        }
    }

    /**
     * Eliminar una configuración específica de la base de datos.
     */
    public function destroy($id)
    {
        try {
            $setting = MercadoPagoSetting::findOrFail($id);
            $setting->delete();

            return response()->json([
                'success' => true,
                'message' => 'Configuración eliminada exitosamente.',
            ], 200);
        } catch (Exception $e) {
            Log::error('Error al eliminar la configuración de MercadoPago', ['message' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'No se pudo eliminar la configuración.',
            ], 500);
        }
    }
}
