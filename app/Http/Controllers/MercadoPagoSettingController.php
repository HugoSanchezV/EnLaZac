<?php

namespace App\Http\Controllers;
use App\Models\MercadoPagoAccount; // Importa el modelo que representa la configuración en la base de datos.
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use MercadoPago; // Importa el SDK de Mercado Pago.
use MercadoPago\Client\MercadoPagoClient;
Use MercadoPago\SDK;

class MercadoPagoSettingController extends Controller
{
    
    /**
     * Constructor para inicializar el SDK de Mercado Pago con el access token correcto.
     */
    public function __construct()
    {
        // Determina el modo (sandbox o live) desde la configuración.
        $mode = config('mercadopago.mode', 'sandbox');

        // Selecciona el access token adecuado según el modo.
        $accessToken = $mode === 'live' 
            ? env('MERCADOPAGO_ACCESS_TOKEN_LIVE') 
            : env('MERCADOPAGO_ACCESS_TOKEN_SANDBOX');

        // Inicializa el SDK de Mercado Pago con el token seleccionado.
        MercadoPago\SDK::setAccessToken($accessToken);
       
    }

    /**
     * Renderiza la vista de edición de las configuraciones de Mercado Pago.
     */
    public function edit()
    {
        // Recupera la configuración existente (si existe).
        $settings = MercadoPagoAccount
        ::first();

        // Renderiza la vista con Inertia, pasando la configuración actual.
        return Inertia::render('Admin/Settings/MercadoPago/Edit', [
            'settings' => $settings,
        ]);
    }

    /**
     * Almacena una nueva configuración de Mercado Pago en la base de datos.
     */
    public function store(Request $request)
    {
        // Valida los datos enviados desde el formulario.
        $request->validate([
            'mode' => 'required|in:sandbox,live', // Modo sandbox o live.
            'sandbox_public_key' => 'required|string', // Clave pública para sandbox.
            'sandbox_access_token' => 'required|string', // Token de acceso para sandbox.
            'live_public_key' => 'nullable|string', // Clave pública para live (opcional).
            'live_access_token' => 'nullable|string', // Token de acceso para live (opcional).
            'currency' => 'required|string|size:3', // Moneda en formato ISO 4217 (ej: USD).
        ]);

        // Crea una nueva configuración con los datos validados.
        $setting = MercadoPagoAccount::create($request->all());

        // Retorna la configuración creada con un código de estado 201 (Creado).
        return response()->json($setting, 201);
    }

    /**
     * Actualiza una configuración existente de Mercado Pago.
     */
    public function update(Request $request)
    {
        // Valida solo los campos opcionales para live.
        $request->validate([
            'live_public_key' => 'nullable|string',
            'live_access_token' => 'nullable|string',
        ]);

        try {
            // Recupera la configuración existente, si no existe, crea una nueva.
            $setting = MercadoPagoAccount::first();
            if (!$setting) {
                // Crea una nueva configuración si no hay ninguna.
                MercadoPagoAccount::create($request->all());
            } else {
                // Actualiza solo los campos proporcionados.
                $setting->update($request->only(['live_public_key', 'live_access_token']));
            }

            // Redirige a la página de edición con un mensaje de éxito.
            return Redirect::route('settings.mercadopago.edit')->with('success', 'Configuración actualizada con éxito');
        } catch (Exception $e) {
            // Redirige con un mensaje de error si ocurre alguna excepción.
            return Redirect::route('settings.mercadopago.edit')->with('error', 'Error al actualizar: ' . $e->getMessage());
        }
    }

    /**
     * Elimina una configuración específica de Mercado Pago.
     */
    public function destroy($id)
    {
        // Busca la configuración por su ID. Lanza un error 404 si no se encuentra.
        $setting = MercadoPagoAccount::findOrFail($id);

        // Elimina la configuración de la base de datos.
        $setting->delete();

        // Retorna una respuesta sin contenido (204).
        return response()->json(null, 204);
    }
}
