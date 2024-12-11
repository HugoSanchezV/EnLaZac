<?php

namespace App\Http\Controllers;

use App\Http\Requests\PaymentSanction\UpdatePaymentSanctionRequest;
use App\Models\PaymentSanction;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaymentSanctionController extends Controller
{
    public function update(Request $request, $id, $url = 'contracts'){

      // dd($request->status);
        try{
            $validatedData = $request->validate(['status' => 'boolean',]);

            $paymentSanction = PaymentSanction::findOrFail($id);

            if($paymentSanction->status != $validatedData['status'])
            {
                $paymentSanction->status = !$paymentSanction->status;
    
                $message = $paymentSanction->status ? 'activada' : 'desactivada';
    
                $paymentSanction->update([
    
                    'status' => $paymentSanction->status,
                ]);

                return redirect()->route('contracts')->with('success', 'Sanción '.$message.' exitosamente');
            }

        }catch(Exception $e)
        {
            return redirect()->route('contracts')->with('error', 'Error al modificar la sanción');
        }
    }

    public function store($id){
        try{
            PaymentSanction::create([
                'contract_id' => $id
            ]);

        }catch(Exception $e){
            return redirect()->route('contracts')->with('error', 'Error al crear la sanción');
        }
    }
    public function store_activate(Request $request, $id, $url = 'contracts'){
       // dd("Sin ID");
        try{

            $validatedData = $request->validate(['status' => 'boolean',]);
            if($validatedData['status'])
            {
                PaymentSanction::create([
                    'contract_id' => $id,
                    'status' => true,
                ]);

                return redirect()->route('contracts')->with('success', 'Sanción activada exitosamente');
            }

        }catch(Exception $e){
            return redirect()->route('contracts')->with('error', 'Error al crear la sanción');
        }
    }

    public function fromPayment($id){
        $payment = PaymentSanction::where('contract_id', $id)->first();

        $payment->update([
            'contract_id' => $id,
            'status' => true
        ]);
    }

    public function getSanction(){
        try{
            return PaymentSanction::with('contract')->where('status', true)->get();
        }catch(Exception $e){
            Log::error("Error en getSanction -> ".$e);
        }
    }

}
