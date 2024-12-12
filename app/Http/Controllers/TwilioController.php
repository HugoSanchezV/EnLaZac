<?php

namespace App\Http\Controllers;

use App\Models\SMSSetting;
use App\Services\SMSService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TwilioController extends Controller
{
    protected $smsService;

    public function __construct(SMSService $smsService)
    {
        $this->smsService = $smsService;
    }

    public function send(Request $request)
    {
        try {
            $smsSettings = SMSSetting::all()->first();
            if (isset($smsSettings) && $smsSettings->active) {
                $request->validate([
                    'phone' => 'required|string',
                    'message' => 'required|string',
                ]);
                Log::info("metodo que envia los mensajes");
                $this->smsService->sendSMS($request->phone, $request->message);
                Log::info("termino el metodo que envia los mensajes");

                return response()->json(['message' => 'SMS enviado con éxito.'], 200);
            } else {
                return response()->json(['message' => 'SMS no esta activo'], 200);
            }
        } catch (Exception $e) {
            return response()->json(['message' => 'Error al enviar el mensaje'], 500);
        }
    }

    public function sendWhats(Request $request)
    {
        Log::info("metodo que envia los mensajes");
        $this->smsService->MessengerWhatsapp();
        Log::info("termino el metodo que envia los mensajes");

        return response()->json(['message' => 'SMS enviado con éxito.'], 200);
    }
}
