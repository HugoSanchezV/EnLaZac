<?php

namespace App\Http\Controllers;

use App\Models\Charge;
use App\Models\Contract;
use App\Models\Device;
use App\Models\Interest;
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
            $rent = Interest::sum('amount');

            $devices = Device::with(['inventorieDevice.contract'])->where('user_id', $userId)->get();
       //     dd($devices);

            if ($devices->count() > 0) {

                $contractIds = [];

                foreach ($devices as $device) {
                    if ($device->inventorieDevice && $device->inventorieDevice->contract) {
                        $contractIds[] = $device->inventorieDevice->contract->id;
                    }
                }
                
                // Verifica si hay IDs de contratos
                if (empty($contractIds)) {
                    return Redirect::route('dashboard')->with('error', 'No hay contratos disponibles');
                }
                
                // Realiza una única consulta para obtener los contratos
                $contracts = Contract::with([
                    'plan',
                    'paymentSanction',
                    'charges' => function ($query) {
                        $query->where('paid', false); // Filtrar charges donde paid sea false
                    },
                ])->whereIn('id', $contractIds) // Filtrar por los IDs recopilados
                  ->where('active', '1') // Filtrar contratos activos
                  ->get();
                
                // Verifica si no se encontraron contratos
                if ($contracts->isEmpty()) {
                    return Redirect::route('dashboard')->with('error', 'No hay contratos disponibles');
                }
                
                // Aquí puedes continuar con la lógica usando $contracts
                
            } else {
                //$contracts = 0;
                return Redirect::route('dashboard')->with('error', 'No hay contratos disponibles');
            }
            // dd($contracts);

            $paypal = PaypalAccount::first();
            $mercadopago = MercadoPagoSetting::select('active')->get()->first();
            
            // Retornar la vista con los datos necesarios

            //   dd($rent);
            return Inertia::render('User/Pays/Pays', [
                // 'charges' => $charges,
                'contracts' => $contracts,
                'paypal' => $paypal ?? ['active' => false],
                'mercadopago' => $mercadopago ?? ['active' => false],
                'rent' => $rent,
            ]);
        } catch (Exception $e) {
            dd($e->getMessage());
            return Redirect::route('dashboard')->with('error', 'Error al mostrar los contratos');
        }
    }
}
