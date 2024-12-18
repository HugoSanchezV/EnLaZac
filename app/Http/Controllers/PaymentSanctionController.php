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

    public function applySanction($id){

        $payment = PaymentSanction::where('contract_id', $id)->first();
        Log::info("Se debe activar la sanción: ".$payment);
        
        $payment->update([
            'status' => false,
            'applied' => true,
        ]);
    }
    public function shutDownSanction($id){
        $payment = PaymentSanction::where('contract_id', $id)->first();
        //Log::info("");
        if($payment->applied){
            $payment->update([
                'contract_id' => $id,
                'status' => false,
                'applied' => false,
            ]);
            
        }

    }

    public function fromPayment($id){
        $payment = PaymentSanction::where('contract_id', $id)->first();
        Log::info("SE APLICO LA SANCION");
        $payment->update([
            'contract_id' => $id,
            'status' => true,
            'applied' => false
        ]);
    }

    public function getSanction(){
        try{
            return PaymentSanction::with('contract')->where('status', true)->where('applied', false)->get();
        }catch(Exception $e){
            Log::error("Error en getSanction -> ".$e);
        }
    }

}
