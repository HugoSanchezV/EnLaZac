<?php

namespace App\Http\Controllers;

use App\Models\MercadoPagoSetting;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Exception;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Illuminate\Support\Facades\Notification;
use App\Notifications\PaymentReceived;
use App\Services\PaymentService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;

class MercadoPagoController extends Controller
{
    public function createPaymentPreference(Request $request)
    {
        Log::info('Creando preferencia de pago');

        $mercadoPagoSettings = MercadoPagoSetting::all()->first();
        // Token de acceso a Mercado Pago
        // $accessToken = "APP_USR-1185876763191395-110614-ff589a305022fcfd67c464b58d746b18-2080399408";
        $accessToken = $mercadoPagoSettings->live_client_secret;

        // Detalles del producto como un array de items
        $items = [];

        foreach ($request->cart as $item) {
            $items[] = [
                "title" => $item["description"],
                "description" => $item["type"] . " " .  ($item["months"] ?? ""),
                "id" => $item["id"],
                "quantity" => 1,
                "unit_price" => $item["amount"],
                "currency_id" => "MXN"
            ];
            //$item["amount"]
        }

        $user = Auth::user();

        $payer = [
            "name" => $user->name,
            "surname" => $user->alias ?? $user->name,
            "email" => $user->email,
        ];

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
            "notification_url" => route('mercadopago.webhook'), // Cambia esta URL cada vez que reinicies ngrok
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

        $mercadoPagoSettings = MercadoPagoSetting::all()->first();
        // Token de acceso a Mercado Pago
        // $accessToken = "APP_USR-1185876763191395-110614-ff589a305022fcfd67c464b58d746b18-2080399408";
        $accessToken = $mercadoPagoSettings->live_client_secret;
        // $accessToken = "TEST-3796733327633492-102515-7f4d7f0ab89b5f70facc784ce720fb04-1345692363";
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

    public function success(Request $request)
    {

        $data = self::getPayment($request);

        $items = [];

        foreach ($data["additional_info"]["items"] as $item) {
            if (trim($item["description"]) === "charge") {

                $items[] = [
                    "description" => $item["title"],
                    "type" => trim($item["description"]),
                    "amount" => $item["unit_price"],
                    "id" => $item["id"],
                ];
            } else {
                $id_contract = explode("-", $item["id"]);
                $contract_and_months = explode(" ", $item["description"]);

                $items[] = [
                    "description" => $item["title"],
                    "months" => intval($contract_and_months[1]),
                    "type" => trim($contract_and_months[0]),
                    "amount" => $item["unit_price"],
                    "contractId" => $id_contract[0],
                ];
            }
        }
        self::update(
            $data["transaction_amount"],
            $items,
            $request->collection_id,
        );

        return Redirect::route('pays')->with('success', 'Se ha realizado el pago, graicas por estarr con nosotros');
    }

    public function getPayment(Request $request)
    {
        // Obtén el payment_id o collection_id del request
        $paymentId = $request->input('collection_id');

        // Access token de Mercado Pago (asegúrate de protegerlo)
        $mercadoPagoSettings = MercadoPagoSetting::all()->first();
        // Token de acceso a Mercado Pago
        // $accessToken = "APP_USR-1185876763191395-110614-ff589a305022fcfd67c464b58d746b18-2080399408";
        $accessToken = $mercadoPagoSettings->live_client_secret;
        // $accessToken = env('MERCADO_PAGO_ACCESS_TOKEN'); // Usa una variable de entorno para mayor seguridad

        // URL para consultar el pago
        $url = "https://api.mercadopago.com/v1/payments/{$paymentId}";

        // Configurar la solicitud cURL
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Authorization: Bearer {$accessToken}"
        ]);

        // Ejecutar la solicitud
        $response = curl_exec($ch);

        // Manejar errores
        if (curl_errno($ch)) {
            $error = curl_error($ch);
            curl_close($ch);
            return response()->json(['error' => 'Error al consultar Mercado Pago: ' . $error], 500);
        }

        // Cerrar la conexión cURL
        curl_close($ch);

        // Decodificar la respuesta JSON
        return json_decode($response, true);
    }

    public function failed(Request $request)
    {
        return Redirect::route('pays')->with('error', 'Hubo un error, no se realizo el pago');
    }

    public function update($amount, $cart, $transaction)
    {
        $payment = new PaymentService();


        $payment->createPayment(
            $amount,
            $cart,
            $transaction,
            "https://www.mercadopago.com.mx/activities/1?q=" . $transaction,
            "Mercado Pago",
            "Mercado Pago",
        );
        $payment->updateDataPayments($cart);
    }
}
