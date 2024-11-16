<?php

namespace App\Services;

use App\Http\Controllers\ChargeController;
use App\Http\Controllers\ContractController;
use App\Http\Controllers\PaymentHistorieController;
use App\Models\PaymentHistorie;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class PaymentService
{
    public function createPayment($amount, $contract, $cart, $transaction, $url)
    {
        try {
            $controller = new PaymentHistorieController();
            $pay = new PaymentHistorie();
            Log::info(json_encode(Auth::user()));
            Log::info(json_encode(Auth::id()));
            $pay->user_id = Auth::id();
            $pay->contract_id = $contract["id"];
            $pay->amount = $amount;
            Log::info('entre al poderoso');
            foreach($cart as $item) {
                $pay->content = $item['description'] . ", " . $item['amount']."\n";
            }
            Log::info('salido del poderoro');

            $pay->payment_method = "PayPal";
            $pay->transaction_id = $transaction;
            $pay->receipt_url = $url;

            $controller->store($pay);
        } catch (Exception $e) {
            Log::info($e->getMessage());
            return throw new Exception();
        }
    }

    public function updateContract($contract, $months)
    {
        try {

            $controller = new ContractController();

            $controller->updateMonths($months, $contract['id']);
        } catch (Exception $e) {
            Log::info("Entro a excepcion en Contract");
            return throw new Exception($e->getMessage());
        }
    }

    public function updateCharge($charges)
    {
        try {
            $charge = new ChargeController();
            foreach ($charges as $cha) {
                $charge->updatePaid($cha['id']);
                if($cha[''] == '')
            }
        } catch (Exception $e) {
            Log::info("Entro a excepcion");
            return throw new Exception($e->getMessage());
        }
    }
}
