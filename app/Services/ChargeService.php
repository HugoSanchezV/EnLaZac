<?php

namespace App\Services;

use App\Http\Controllers\ChargeController;
use App\Http\Controllers\InterestsController;
use App\Models\Charge;
use App\Models\Contract;

class ChargeService
{
    public function createChargeCourtDate(Contract $contract)
    {
        $controller = new ChargeController();
        $cargo = new Charge();
        $controllerInterest = new InterestsController();
        
        //Set data
        $interest = $controllerInterest->getInterest("fuera-fecha");

        if(!$interest)
        {
            $controllerInterest->createInterestCourtDate();
            $interest = $controllerInterest->getInterest("fuera-fecha");
        }

        $cargo->contract_id = $contract->id;
        $cargo->description = "No pago el servicio durante el mes";
        $cargo->amount = $interest->amount;
        $cargo->paid = false;
        
        //$this->info($cargo);
        $controller->store_schedule($cargo);
    }

    public function createChargeMounthsDebt(Contract $contract)
    {
        $controller = new ChargeController();
        $cargo = new Charge();
        $controllerInterest = new InterestsController();
        $interest = $controllerInterest->getInterest("recargo-mes");

        if(!$interest)
        {
            $controllerInterest->createInterestMounthsDebt();
            $interest = $controllerInterest->getInterest("recargo-mes");
        }
        //Set data
        $cargo->contract_id = $contract->id;
        $cargo->description = "No pagó antes del día de corte";
        $cargo->amount = $interest->amount;
        $cargo->paid = false;
        
        //$this->info($cargo);
        $controller->store_schedule($cargo);
    }
}
