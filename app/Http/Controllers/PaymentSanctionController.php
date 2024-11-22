<?php

namespace App\Http\Controllers;

use App\Http\Requests\PaymentSanction\UpdatePaymentSanctionRequest;
use App\Models\PaymentSanction;
use Exception;
use Illuminate\Http\Request;

class PaymentSanctionController extends Controller
{
    public function update(UpdatePaymentSanctionRequest $request, $id){

        try{
            $paymentSanction = PaymentSanction::findOrFail($id);
            $validatedData = $request->validate();

            $paymentSanction->update($validatedData);
        }catch(Exception $e)
        {

        }
    }

    public function store($id){
        try{
            PaymentSanction::create([
                'contract_id' => $id
            ]);

        }catch(Exception $e){

        }
    }

}
