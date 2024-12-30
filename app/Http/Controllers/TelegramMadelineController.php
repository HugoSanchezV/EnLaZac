<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\TelegramService;
use Illuminate\Support\Facades\Validator;

class TelegramMadelineController extends Controller
{
    protected $telegramService;

    public function __construct(TelegramService $telegramService)
    {
        $this->telegramService = $telegramService;
    }
    /**
     * Enviar un mensaje a un chat específico.
     */
    public function sendMessage(Request $request)
    {
        $request->validate([
            'peer' => 'required|string',
            'message' => 'required|string',
        ]);

        try {
            // $this->telegramService->sendMessage( $request->peer,  $request->message);
            $this->telegramService->sendMessage('7866871450',  'holaaa');
            return response()->json(['status' => 'Mensaje enviado correctamente.']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al enviar el mensaje: ' . $e->getMessage()], 500);
        }
    }

    public function importContact(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'phone' => 'required|string|regex:/^\+\d+$/',
            'first_name' => 'required|string|max:255',
            'last_name' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $phone = $request->input('phone');
        $firstName = $request->input('first_name');
        $lastName = $request->input('last_name', '') ?? '';

        try {
            $userId = $this->telegramService->importContact($phone, $firstName, $lastName);

            if ($userId) {
                return response()->json([
                    'message' => 'Contacto importado exitosamente.',
                    'user_id' => $userId,
                ], 200);
            } else {
                return response()->json([
                    'message' => 'Contacto importado, pero no se pudo obtener el User ID.',
                ], 200);
            }
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error al importar contacto: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function destroyContact()
    {
        try {
            $this->telegramService->deleteHistory('7866871450');
            $response = $this->telegramService->deleteContact('7866871450');

            if ($response) {
                return response()->json([
                    'message' => 'Contacto eliminado exitosamente.',
                ], 200);
            } else {
                return response()->json([
                    'message' => 'Contacto importado, pero no se pudo obtener el User ID.',
                ], 200);
            }
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error al eliminar contacto: ' . $e->getMessage(),
            ], 500);
        }
    }
}
