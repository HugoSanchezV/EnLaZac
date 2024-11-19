<?php

namespace App\Http\Controllers;

use App\Models\Charge;
use App\Models\Contract;
use App\Models\Device;
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

        // Obtener todos los contratos del cliente
        try {
            $device = Device::where('user_id', $userId)->get();
            //dd($device);
            $deviceIds = $device->pluck('id');
            //  dd($deviceIds);
            $contracts = Contract::with('plan', 'device')->where('device_id', $deviceIds)->get();
            // dd($contracts);
            if ($contracts->isEmpty()) {
                // Si no hay contratos, inicializar valores
                $charges = [];
            } else {

                // Obtener todos los cargos pendientes para los contratos
                $contractIds = $contracts->pluck('id');
                $charges = Charge::whereIn('contract_id', $contractIds)
                    ->where('paid', false)
                    ->get();
            }
            // Retornar la vista con los datos necesarios
            return Inertia::render('User/Pays/Pays', [
                'charges' => $charges,
                'contracts' => $contracts,
            ]);
        } catch (Exception $e) {
            return Redirect::route('dashboard')->with('error', 'No fue posible cargar los pagos');
        }
    }
}
