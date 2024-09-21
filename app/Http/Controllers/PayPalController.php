<?php

namespace App\Http\Controllers;
use Srmklive\PayPal\Services\PayPal as PayPalClient;
use Illuminate\Http\Request;

class PayPalController extends Controller
{
    public function createOrder()
    {
        $paypalModule = new PayPalClient;
        $paypalModule->setApiCredentials(config('paypal'));
        
        $token = $paypalModule->getAccessToken();
        $paypalModule->setAccessToken($token);
        $order = $paypalModule->createOrder([
            "intent" => "CAPTURE",  
            "purchase_units" => [
                [
                    "amount" => [
                        "currency_code" => "USD",  
                        "value" => "100.00" 
                    ]
                ]
            ]
        ]);

        return response()->json($order);
    }

    public function captureOrder(Request $request)
    {
        $paypalModule = new PayPalClient;
        $paypalModule->setApiCredentials(config('paypal'));
        $token = $paypalModule->getAccessToken();
        $paypalModule->setAccessToken($token);

        $response = $paypalModule->capturePaymentOrder($request->orderID);

        if ($response['status'] === 'COMPLETED') {
            return response()->json(['status' => 'success']);
        }

        return response()->json(['status' => 'error'], 500);
    }
}