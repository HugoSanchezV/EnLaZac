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

            if ($devices->count() > 0) {

                $contracts = [] ; 

                $contractIds = [];
                foreach($devices as $device){
                    if($device->inventorieDevice){
                        if($device->inventorieDevice->contract)
                        {
                            $contractIds [] = $device->inventorieDevice->contract->id;
                        }
                    }

                }  

                $contracts = Contract::with([
                    'plan',
                    'charges' => function ($query) {
                        $query->where('paid', false); // Filtrar charges donde paid sea false
                    },
                ])->findOrFail($contractIds)->where('active', 1);

                if($contracts->count() == 0){
                    return Redirect::route('dashboard')->with('error', 'No hay contratos disponibles');

                }
                
            } else 
            {
                //$contracts = 0;
                return Redirect::route('dashboard')->with('error', 'No hay contratos disponibles');

            }
            // dd($contracts);

            $paypal = PaypalAccount::first();
            $mercadopago = MercadoPagoSetting::select('active')->first();
            // Retornar la vista con los datos necesarios
            return Inertia::render('User/Pays/Pays', [
               // 'charges' => $charges,
                'contracts' => $contracts,
                'paypal' => $paypal,
                'mercadopago' => $mercadopago,
            ]);
        } catch (Exception $e) {
           dd($e);
           return Redirect::route('dashboard')->with('error', 'Error al mostrar los contratos');
        }
    }
}
