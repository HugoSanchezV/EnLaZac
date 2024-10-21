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
        $transaction_id = $response['purchase_units'][0]['payments']['captures'][0]['id'];
        if ($response['status'] === 'COMPLETED') {
            self::update($request->amount, 
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
    public function update ($amount, $months, $contract, $charges, $cart, $transaction)
    {
        $payment = new PaymentService();

        $payment->createPayment(
        $amount,  
        $contract, 
        $cart, 
        $transaction,
        "https://www.paypal.com/activity/payment/{$transaction}");

        if($months > 0)
        {
            $payment->updateContract($contract, $months);
        }
        
        if(count($charges) > 0)
        {
            $payment->updateCharge($charges);
        }
        
    }

}