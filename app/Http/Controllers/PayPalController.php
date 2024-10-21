<?php

namespace App\Http\Controllers;

use App\Services\PaymentService;
use Srmklive\PayPal\Services\PayPal as PayPalClient;
use Illuminate\Http\Request;

class PayPalController extends Controller
{
    public function createOrder($amount)
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
                        "currency_code" => "MXN",  
                        "value" => $amount 
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
            self::update($request->amount, 
            $request->mounths, 
            $request->contract, 
            $request->charges,
            $request->cart);
            return response()->json(['status' => 'success']);
        }

        return response()->json(['status' => 'error'], 500);
    }
    public function update ($amount, $months, $contract, $charges, $cart)
    {
        $payment = new PaymentService();
        $payment->createPayment($amount, $months, $contract, $cart);
        $payment->updateContract($contract, $months);
        $payment->updateCharge($charges);
    }

}