<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Exception;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Illuminate\Support\Facades\Notification;
use App\Notifications\PaymentReceived;

class MercadoPagoController extends Controller
{
    public function createPaymentPreference(Request $request)
    {
        Log::info('Creando preferencia de pago');

        // Token de acceso a Mercado Pago
        $accessToken = "APP_USR-1185876763191395-110614-ff589a305022fcfd67c464b58d746b18-2080399408";
        
        // Detalles del producto como un array de items
        $items = [
            [
                "title" => "Producto de ejemplo",
                "quantity" => 1,
                "unit_price" => 100.0,
                "currency_id" => "MXN"
            ]
        ];

        // Información del comprador
        $payer = [
            "name" => "hols",
            "surname" => "hugo",
            "email" => "i@admin.com",
        ];

        // Crear los datos de la solicitud de preferencia
        $requestData = [
            "items" => $items,
            "payer" => $payer,
            "payment_methods" => [
                "excluded_payment_methods" => [],
                "installments" => 12,
                "default_installments" => 1
            ],
            "back_urls" => [
                "success" => route('mercadopago.success'),
                "failure" => route('mercadopago.failed')
            ],
            "notification_url" => "https://abcd1234.ngrok.io/mercadopago/webhook", // Cambia esta URL cada vez que reinicies ngrok
            "statement_descriptor" => "Servicio de red",
            "external_reference" => "1234567890",
            "expires" => false,
            "auto_return" => 'approved'
        ];

        // Crear un cliente GuzzleHTTP
        $client = new Client();

        try {
            // Enviar la solicitud a Mercado Pago
            $response = $client->post('https://api.mercadopago.com/checkout/preferences', [
                'headers' => [
                    'Authorization' => "Bearer $accessToken",
                    'Content-Type' => 'application/json',
                ],
                'json' => $requestData,
                'verify' => false, // Desactiva la verificación SSL (solo para desarrollo)
            ]);

            // Obtener y decodificar la respuesta de Mercado Pago
            $preference = json_decode($response->getBody()->getContents(), true);
            Log::info('Preferencia creada exitosamente', ['preference' => $preference]);

            // Redirigir al usuario a la URL de pago usando Inertia
            return Inertia::location($preference['init_point']);
        } catch (Exception $e) {
            // Registrar cualquier error que ocurra
            Log::error('Error inesperado', ['message' => $e->getMessage()]);

            // Enviar una respuesta de error en formato JSON para manejarla en el frontend si es necesario
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    // Método webhook para manejar notificaciones de Mercado Pago
    public function webhook(Request $request)
    {
        Log::info('Webhook recibido', $request->all());

        // Verificar que el estado del pago sea "approved"
        if ($request->type === 'payment') {
            $paymentId = $request->data['id'];
            $paymentStatus = $this->getPaymentStatus($paymentId);

            if ($paymentStatus === 'approved') {
                $paymentAmount = $request->data['transaction_amount'];

                Log::info('Pago aprobado y notificación enviada al usuario');
            }
        }

        return response()->json(['status' => 'success'], 200);
    }

    // Función auxiliar para obtener el estado del pago a partir del ID
    private function getPaymentStatus($paymentId)
    {
        $accessToken = "TEST-3796733327633492-102515-7f4d7f0ab89b5f70facc784ce720fb04-1345692363";
        $client = new Client();

        try {
            $response = $client->get("https://api.mercadopago.com/v1/payments/{$paymentId}", [
                'headers' => [
                    'Authorization' => "Bearer $accessToken",
                ],
                'verify' => false,
            ]);

            $paymentData = json_decode($response->getBody()->getContents(), true);

            return $paymentData['status'] ?? null;
        } catch (Exception $e) {
            Log::error('Error al obtener el estado del pago', ['message' => $e->getMessage()]);
            return null;
        }
    }
}
