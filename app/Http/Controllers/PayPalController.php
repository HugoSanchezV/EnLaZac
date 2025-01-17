<?php

namespace App\Http\Controllers;

use App\Models\PaypalAccount;
use App\Services\PaymentService;
use Exception;
use Srmklive\PayPal\Services\PayPal as PayPalClient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
class PayPalController extends Controller
{
    public function createOrder(Request $request)
    {
        Log::info('Este es el log[ '.$request->amount);
        $paypalAccount = PaypalAccount::first();

        // Log::info("Live Client ID: " . $paypalAccount->live_client_id);
        // Log::info("Live Client Secret: " . $paypalAccount->live_client_secret);
        // $token = "";
        try {
            $paypalModule = new PayPalClient;
            $paypalAccount = PaypalAccount::first();
            
            $paypalAccount = PaypalAccount::first();

            $paypalModule->setApiCredentials([
                'mode'    => 'live', // Por defecto 'sandbox'
                'sandbox' => [
                    'client_id'     => $paypalAccount->live_client_id ?? '',
                    'client_secret' => $paypalAccount->live_client_secret ?? '',
                    'app_id'        => $paypalAccount->app_id ?? '',
                ],
                'live' => [
                    'client_id'     => $paypalAccount->live_client_id ?? '',
                    'client_secret' => $paypalAccount->live_client_secret ?? '',
                    'app_id'        => $paypalAccount->app_id ?? '',
                ],
                'payment_action' => $paypalAccount->payment_action ?? 'Sale',
                'currency'       => $paypalAccount->currency ?? 'MXN',
                'notify_url'     => $paypalAccount->notify_url ?? '',
                'locale'         => $paypalAccount->locale ?? 'en_US',
                'validate_ssl'   => $paypalAccount->validate_ssl ?? true,
            ]);

            
            $token = $paypalModule->getAccessToken();

            Log::error($paypalModule->getAccessToken());
           //Log::info(json_encode($token));

            $paypalModule->setAccessToken($token);

            $order = $paypalModule->createOrder([
                "intent" => "CAPTURE",
                "purchase_units" => [
                    [
                        "amount" => [
                            "currency_code" => "MXN",
                            "value" => strval($request->amount)
                        ]
                    ]
                ]
            ]);
            //strval($request->amount)
        } catch (Exception $e) {
            Log::error('Error en la creación de la orden de PayPal: ' . $e->getMessage());

            $errorData = [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ];

            return response()->json($errorData);
        }
        return response()->json($order);
    }

    public function captureOrder(Request $request)
    {
        // Log::info("Entre al metodo");
        //Log::info(json_encode($request->all()));
        //Log::info("estas en el final de todo ");

        try {

            $paypalModule = new PayPalClient;

            $paypalAccount = PaypalAccount::first();

            $paypalModule->setApiCredentials([
                'mode'    => 'live', // Por defecto 'sandbox'
                'sandbox' => [
                    'client_id'     => $paypalAccount->live_client_id ?? '',
                    'client_secret' => $paypalAccount->live_client_secret ?? '',
                    'app_id'        => $paypalAccount->app_id ?? '',
                ],
                'live' => [
                    'client_id'     => $paypalAccount->live_client_id ?? '',
                    'client_secret' => $paypalAccount->live_client_secret ?? '',
                    'app_id'        => $paypalAccount->app_id ?? '',
                ],
                'payment_action' => $paypalAccount->payment_action ?? 'Sale',
                'currency'       => $paypalAccount->currency ?? 'MXN',
                'notify_url'     => $paypalAccount->notify_url ?? '',
                'locale'         => $paypalAccount->locale ?? 'en_US',
                'validate_ssl'   => $paypalAccount->validate_ssl ?? true,
            ]);
            // Log::info('paypal modulo completo');

            $token = $paypalModule->getAccessToken();
            // Log::info('Se obtuvo el token');

            $paypalModule->setAccessToken($token);
            // Log::info('setAccesstoekn compleatdo con exto');


            $response = $paypalModule->capturePaymentOrder($request->orderID);
            // Log::info('Se obtuvo respuesra');
            // Log::info($response);

            $transaction_id = $response['purchase_units'][0]['payments']['captures'][0]['id'];
            // Log::info("This is the log");
            // Log::info($transaction_id);
            // Log::info("Arriba log");

            if ($response['status'] === 'COMPLETED') {
                Log::info("Proceso completado");
                self::update(
                    $request->amount,
                    $request->cart,
                    $transaction_id,
                );

                // return redirect()->route('pays')->with('success', 'Se ha realizado la operación con éxito');
                // return response()->json(['status' => 'success'], 200);
                return response()->json(['status' => 'success', 'redirect' => route('pays')], 200);
                // return redirect()->route('pays')->with('success', 'Se ha realizado la operación con éxito');
                //return Redirect::route('pays')->with('success', 'Se ha realizado la operación con exito');
            }
        } catch (Exception $e) {
            //Log::info("Error al hacer el pago " . $e->getMessage());
            //Log::info("");
            // return response()->json(['status' => 'error'], 500);
            return redirect()->route('pays')->with('error', 'Hubo un problema al procesar el pago. Inténtalo de nuevo.');
        }
    }
    public function update($amount, $cart,  $transaction)
    {
        $payment = new PaymentService();

        $payment->createPayment(
            $amount,
            $cart,
            $transaction,
            "https://www.paypal.com/activity/payment/{$transaction}",
            "PayPal"
        );

        // if ($months > 0) {
        //     $payment->updateContract($contract, $months);
        // }

        // if (count($charges) > 0) {
        //     $payment->updateCharge($charges);
        // }

        $payment->updateDataPayments($cart);
    }
}
