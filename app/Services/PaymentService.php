<?php

namespace App\Services;

use App\Http\Controllers\ChargeController;
use App\Http\Controllers\ContractController;
use App\Http\Controllers\InventorieDevicesController;
use App\Http\Controllers\PaymentHistorieController;
use App\Models\Device;
use App\Models\DeviceHistorie;
use App\Models\InventorieDevice;
use App\Models\PaymentHistorie;
use App\Models\Router;
use App\Services\RouterOSService;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PaymentService
{
    public function createPayment($amount, $contract, $cart, $transaction, $url)
    {
        $controller = new PaymentHistorieController();
        $pay = new PaymentHistorie();

        $pay->user_id = Auth::id();
        $pay->contract_id = $contract->id;
        $pay->amount = $amount;
        $pay->content = $cart;
        $pay->payment_method = "PayPal";
        $pay->transaction_id = $transaction;
        $pay->receipt_url = $url;

        $controller->store($pay);
    }

    public function updateContract($contract, $months)
    {
        $contract = new ContractController();

        $contract->updateMonths($contract, $months);
        
    }

    public function updateCharge($charges)
    {
        $charge = new ChargeController();
        foreach($charges as $charge)
        {
            $charge->updatePaid($charge->id);
        }
        
    }
}
