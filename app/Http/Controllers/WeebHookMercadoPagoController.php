<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WeebHookMercadoPagoController extends Controller
{
    public function webhookMercadoPagoPago(Request $request)
    {
        Log::info("entre al metodo");
        Log::info(json_encode($request->all()));
        return response('success', 200);
    }
}
