<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use App\Services\PaymentService;
use Exception;
use Srmklive\PayPal\Services\PayPal as PayPalClient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PayPalController extends Controller
{
    public function createOrder(Request $request)
    {
        Log::info(Auth::id());


        try {
            $paypalModule = new PayPalClient;

            $paypalModule->setApiCredentials(config('paypal'));

            $token = $paypalModule->getAccessToken();

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
        } catch (Exception $e) {
        //    Log::error('Error en la creación de la orden de PayPal: ' . $e->getMessage());

            $errorData = [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
               // 'MADAFALE' => $token
            ];

            return response()->json($errorData);
        }
        return response()->json($order);
    }

    public function captureOrder(Request $request)
    {
        $paypalModule = new PayPalClient;

        Log::info('Se obtuvo el cliente');
        $paypalModule->setApiCredentials(config('paypal'));
       // Log::info('paypal modulo completo');

        $token = $paypalModule->getAccessToken();
      //  Log::info('Se obtuvo el token');

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
            self::update(
                $request->amount,
                $request->mounths,
                $request->contract,
                $request->charges,
                $request->cart,
                $transaction_id,
            );
            return response()->json(['status' => 'success']);
        }

        return response()->json(['status' => 'error'], 500);
    }
    public function update($amount, $months, $contract, $charges, $cart, $transaction)
    {
        $payment = new PaymentService();

        $payment->createPayment(
            $amount,
            $contract,
            $cart,
            $transaction,
            "https://www.paypal.com/activity/payment/{$transaction}"
        );

        if ($months > 0) {
            $payment->updateContract($contract, $months);
        }

        if (count($charges) > 0) {
            $payment->updateCharge($charges);
        }
    }
}
