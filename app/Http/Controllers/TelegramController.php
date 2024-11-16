<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class TelegramController extends Controller
{
    //
    public function webhook(Request $request)
    {
        // $data = $request->all();

        // // Verifica que el mensaje es un /start
        // Log::info("Entre al metodo");
        // // return response()->json([
        // //     'status' => '200',
        // //     'response' => 'entre'
        // // ]);

        // Log::info(json_encode($data));
        // if (isset($data['message']['text']) && $data['message']['text'] === '/start') {

        //     Log::info("Se hixo un start");

        //     $chatId = $data['message']['chat']['id'];
        //     Log::info("Este es el id");
        //     Log::info($chatId);

        //     $username = $data['message']['from']['username'] ?? null;
        //     Log::info("usuario");
        //     Log::info($username);

        //     $this->sendContactRequestButton($chatId);
        // }

        // return response()->json([
        //     'status' => 'ok',
        //     'response' => 'success'
        // ]);
    }

    private function sendContactRequestButton($chatId)
    {
        // $token = env('TELEGRAM_BOT');
        // $url = "https://api.telegram.org/bot$token/sendMessage";

        // Log::info("Esta enviando el mensaje");
        // $keyboard = [
        //     'keyboard' => [[
        //         [
        //             'text' => 'Enviar mi número de contacto',
        //             'request_contact' => true // Esto solicita el número del usuario
        //         ]
        //     ]],
        //     'resize_keyboard' => true,
        //     'one_time_keyboard' => true
        // ];

        // Log::info("Termino de enviarlo");
        // $response = Http::post($url, [
        //     'chat_id' => $chatId,
        //     'text' => 'Por favor, comparte tu número de contacto.',
        //     'reply_markup' => json_encode($keyboard)
        // ]);

        // Log::info(json_encode($response));
        // return $response->ok();
    }
}
