<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\TelegramBotService;

class TelegramController extends Controller
{
    protected $telegramBotService;

    public function __construct(TelegramBotService $telegramBotService)
    {
        $this->telegramBotService = $telegramBotService;
    }

    /**
     * Enviar un mensaje a través del bot de Telegram.
     */
    public function sendMessage(Request $request)
    {
        // Validar los datos entrantes
        $request->validate([
            'message' => 'required|string|max:1000',
            'chat_id' => 'required|string',
        ]);

        // Enviar el mensaje usando el servicio de Telegram
        $success = $this->telegramBotService->sendMessage($request->chat_id, $request->message);

        return response()->json([
            'success' => $success,
            'message' => $success ? 'Mensaje enviado con éxito' : 'Hubo un error al enviar el mensaje',
        ]);
    }
}