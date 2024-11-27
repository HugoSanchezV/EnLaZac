<?php

namespace App\Services;

use App\Http\Controllers\ChargeController;
use App\Http\Controllers\ContractController;
use App\Http\Controllers\ExtendContractController;
use App\Http\Controllers\PaymentHistorieController;
use App\Models\ExtendContract;
use App\Models\PaymentHistorie;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class PaymentService
{
    public function createPayment($amount, $cart, $transaction, $url, $method = "Paypal", $worker = "Paypal")
    {
        try {


            $controller = new PaymentHistorieController();
            $pay = new PaymentHistorie();
            $pay->user_id = Auth::id();
            $pay->worker = $worker;
            $pay->amount = $amount;
            $pay->content = $cart;
            $pay->payment_method = $method;

            $pay->transaction_id = $transaction;
            $pay->receipt_url = $url;

            $controller->store($pay);
        } catch (Exception $e) {
            Log::info('Error en createPayment payment service hugo ' . $e->getMessage());
            return throw new Exception();
        }
    }

    public function updateContract($id, $months)
    {
        try {
            $controller = new ContractController();
            $controllerExtend = new ExtendContractController();


            $controllerExtend->shutDownExtend($id);
            $controller->updateMonths($months, $id);
        } catch (Exception $e) {
            Log::info("Entro a excepcion en Contract PAYMENTSERVICE");
            return throw new Exception($e->getMessage());
        }
    }

    public function updateCharge1($charges)
    {
        try {
            $charge = new ChargeController();
            foreach ($charges as $cha) {
                $charge->updatePaid($cha['id']);
                if ($cha[''] == '');
            }
        } catch (Exception $e) {
            Log::info("Entro a excepcion");
            return throw new Exception($e->getMessage());
        }
    }

    public function updateCharge($cha)
    {
        try {
            $charge = new ChargeController();
            $charge->updatePaid($cha['id']);
        } catch (Exception $e) {
            Log::info("Entro a excepcion");
            return throw new Exception($e->getMessage());
        }
    }

    public function updateDataPayments($cart)
    {
        try {
            foreach ($cart as $item) {
                if ($item["type"] === "contract") {
                    self::updateContract($item["contractId"], $item["months"]);
                }else if ($item["type"] === "charge") {
                    self::updateCharge($item);
                }
            }
        } catch (Exception $e) {
            Log::info("ERROR al actualizar lso datos del pago " . $e->getMessage());
            return throw new Exception("Error en update data payment " . $e->getMessage());
        }
    }
}
