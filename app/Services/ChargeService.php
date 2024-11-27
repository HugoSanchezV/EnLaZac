<?php

namespace App\Services;

use App\Http\Controllers\ChargeController;
use App\Http\Controllers\InterestsController;
use App\Models\Charge;
use App\Models\Contract;
use App\Models\RuralCommunity;

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
        $cargo->description = "fuera-corte";
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
        $cargo->description = "recargo-mes";
        $cargo->amount = $interest->amount;
        $cargo->paid = false;
        
        //$this->info($cargo);
        $controller->store_schedule($cargo);
    }

    public function createChargeInstallation(Contract $contract){


        $controller = new ChargeController();
        $cargo = new Charge();
        $community = RuralCommunity::findOrFail($contract->rural_community_id);

        $cargo->contract_id = $contract->id;
        $cargo->description = "instalacion-inicial";
        $cargo->amount = $community->installation_cost;
        $cargo->paid = false;

        $controller->store_schedule($cargo);

    }
}
