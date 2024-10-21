<?php

namespace App\Services;

use App\Http\Controllers\ChargeController;
use App\Http\Controllers\ContractController;
use App\Http\Controllers\InventorieDevicesController;
use App\Http\Controllers\PaymentHistorieController;
use App\Models\Device;
use App\Models\DeviceHistorie;
use App\Models\InventorieDevice;
use App\Models\Router;
use App\Services\RouterOSService;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PaymentService
{
    public function createPayment($amount, $months, $contract, $charges)
    {
        $controller = new PaymentHistorieController();

        $controller->create();
    }

    public function updateContract($contract, $months)
    {
        $contract = new ContractController();
    }

    public function updateCharge($charges)
    {
        $charge = new ChargeController();
        foreach($charges as $charge)
        {

        }
        $charge->updatePaid();
    }
}
