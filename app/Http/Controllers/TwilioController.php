<?php

namespace App\Http\Controllers;

use App\Services\SMSService;
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
        $request->validate([
            'phone' => 'required|string',
            'message' => 'required|string',
        ]);
        Log::info("metodo que envia los mensajes");
        $this->smsService->sendSMS($request->phone, $request->message);
        Log::info("termino el metodo que envia los mensajes");

        return response()->json(['message' => 'SMS enviado con éxito.'], 200);
    }

    public function sendWhats(Request $request)
    {
        Log::info("metodo que envia los mensajes");
        $this->smsService->MessengerWhatsapp();
        Log::info("termino el metodo que envia los mensajes");

        return response()->json(['message' => 'SMS enviado con éxito.'], 200);
    }


}
