<?php

namespace App\Http\Controllers;

use App\Models\Charge;
use App\Models\Contract;
use App\Models\Plan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class PayController extends Controller
{
    //

    public function index() {
        $userId = Auth::id();
        $contract = Contract::where('user_id', $userId)->first();
        if(is_null($contract))
        {
            $charges = 0;
            $price = 0;
        }else{
            $plan = Plan::where('id',$contract->plan_id)->first();
            //dd($plan);
            $price = $plan->price;
            $charges = Charge::where('contract_id',$contract->id)
                             ->where('paid', false)
                             ->get();
        }
        


       // dd($price);
        return Inertia::render('User/Pays/Pays',[
            'charges' => $charges,
            'cost_service' => $price,
            'contract' => $contract
        ]);
    }
}
