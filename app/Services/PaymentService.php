<?php

namespace App\Services;

use App\Http\Controllers\ChargeController;
use App\Http\Controllers\ContractController;
use App\Http\Controllers\DevicesController;
use App\Http\Controllers\ExtendContractController;
use App\Http\Controllers\PaymentHistorieController;
use App\Http\Controllers\PaymentSanctionController;
use App\Models\Charge;
use App\Models\Contract;
use App\Models\ExtendContract;
use App\Models\PaymentHistorie;
use App\Models\PaymentSanction;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class PaymentService
{
    public function createPayment($amount, $cart, $transaction, $url, $method = "Paypal", $worker = "Paypal", $user_id = null)
    {
        try {
            $controller = new PaymentHistorieController();
            $pay = new PaymentHistorie();
            $pay->user_id = $user_id ?? Auth::id();
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

    public function updateContract($id, $months, $isRent)
    {
        try {
            $controller = new ContractController();
            $controllerExtend = new ExtendContractController();
            $controllerDevice = new DevicesController();
            $controllerSanction = new PaymentSanctionController();
            $contract = Contract::findOrFail($id);

            $controllerExtend->shutDownExtend($id);
            $controller->updateMonths($months, $id);
            $controllerSanction->shutDownSanction($id);



            if(!$isRent){
                Log::info("SE VA A CONECTAR: ".$isRent);
                $controllerDevice->connectUser($contract);
            }else{
                Log::info("SE VA A DESCONECTAR: ".$isRent);
                $controllerDevice->disconectUser($contract);
            }
        } catch (Exception $e) {
            Log::info("Entro a excepcion en Contract PAYMENTSERVICE");
            return throw new Exception($e->getMessage());
        }
    }

    public function updateDataPayments($cart)
    {
        try {
            $chargeController = new ChargeController();
            foreach ($cart as $item) {
                if ($item["type"] === "contract") {
                    // Actualizar el contrato
                    Log::info("Contracto".$item["id"].", Meses:".$item["months"]);
                    self::updateContract($item["id"], $item["months"], false);
    
                    // Consultar todos los cargos asociados al contrato
                    
                }else if($item["type"] === "individual-charge"){
                    
                    $chargeController->paidInstallation($item["id"]);
                    
                }else if($item["type"] === "charge"){

                    $chargeController->paidCharge($item["id"]);
                }else if($item["type"] === "rent"){

                    self::updateContract($item["id"], $item["months"], true);
                }
            }
        } catch (Exception $e) {
            Log::info("ERROR al actualizar los datos del pago: " . $e->getMessage());
            throw new Exception("Error en update data payment: " . $e->getMessage());
        }
    }

}
