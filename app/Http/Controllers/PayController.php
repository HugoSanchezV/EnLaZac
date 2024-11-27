<?php

namespace App\Http\Controllers;

use App\Models\Charge;
use App\Models\Contract;
use App\Models\Device;
use App\Models\MercadoPagoSetting;
use App\Models\PaypalAccount;
use App\Models\Plan;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class PayController extends Controller
{
    //
    public function index()
    {

        $userId = Auth::id();
        $charges = null;
        $contracts = null;
        // Obtener todos los contratos del cliente
        try {
            $devices = Device::with(['inventorieDevice.contract'])->where('user_id', $userId)->get();
            
           //dd($devices);
           //dd($deviceIds);
            if ($devices->count() > 0) {

                $contracts = [] ; 
                $charges = [];

                
                foreach($devices as $device){
                    if($device->inventorieDevice)
                    {
                        if($device->inventorieDevice->contract)
                        {
                            $contractIds = $device->inventorieDevice->contract->pluck('id');
                        }
                    }
                }  
                $contractIds = $device->inventorieDevice->contract->pluck('id')->toArray();

                //dd($contractIds);
                $contracts = Contract::with('plan', 'charges')->findOrFail($contractIds);

                $charges = Charge::whereIn('contract_id', $contractIds)->get();

               // dd($charges);
              ///  dd($contracts->count());

               // dd($contracts->count());

                // foreach($contracts as $contract){
                //     $charges [] = $contract->charges; 
                // }
                //dd($charges);
                
                
                // $deviceIds = $device->pluck('device_id');
                // $contracts = Contract::with('plan', 'inventorieDevice')->where('inv_device_id', $deviceIds)->get();

                // if ($contracts->isEmpty()) {
                //     // Si no hay contratos, inicializar valores
                //     $charges = [];
                // } else {

                //     // Obtener todos los cargos pendientes para los contratos
                //     $contractIds = $contracts->pluck('id');
                //     $charges = Charge::whereIn('contract_id', $contractIds)
                //         ->where('paid', false)
                //         ->get();
                // }
            } else 
            {
                $charges = null;
                $contracts = null;
            }
            // dd($contracts);

            $paypal = PaypalAccount::first();
            $mercadopago = MercadoPagoSetting::select('active')->first();
            // Retornar la vista con los datos necesarios
            return Inertia::render('User/Pays/Pays', [
                'charges' => $charges,
                'contracts' => $contracts,
                'paypal' => $paypal,
                'mercadopago' => $mercadopago,
            ]);
        } catch (Exception $e) {
            dd($e);
            return Redirect::route('dashboard')->with('error', 'No ha dispositivo asiganado');
        }
    }
}
