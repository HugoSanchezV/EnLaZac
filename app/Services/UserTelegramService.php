<?php

namespace App\Services;

use App\Models\TelegramAccount;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Log;

class UserTelegramService
{
    static function createContactTelegramSendMessage(array $data, TelegramService $telegramService, $message = "Hola Bienvenido a EnLaZac")
    {
        try {
            $userId = $telegramService->importContact($data['phone'], $data['name'], $data['alias']);

            if ($userId && isset($userId["imported"][0]["user_id"])) {
                $telegramService->sendMessage($userId["imported"][0]["user_id"], $message . " " . $data['name']);

                return $userId["imported"][0]["user_id"];
            }
        } catch (Exception $e) {
            Log::error("Error al crear contacto en telegram " . $e->getMessage());
            return false;
        }
        return false;
    }

    static function sendMessage(TelegramService $telegramService, String $chat_id, String $message)
    {
        try {
            $telegramService->sendMessage($chat_id, $message);
            return true;
        } catch (\Exception $e) {
            Log::error("Error al envair el mensaje " . $e->getMessage());
            return false;
        }
    }

    static function sendMessageWithUserId(TelegramService $telegramService, $user_id)
    {
        try {
            $account = TelegramAccount::where('user_id', $user_id);
            if ($account) {
                self::sendMessage(
                    $telegramService,
                    $account->chat_id,
                    "Tu mensualida de internet en EnLaZac a terminado, por favor revisa tu plan para reconectar"
                );
                return true;
            } else {
                Log::error("El usuario con id " . $user_id . "no ha sido agregado a telegram o no tiene cuenta");
                return false;
            }
        } catch (Exception $e) {
            Log::error("Error al enviar el mensaje por telegram en evento Check contract " . $e->getMessage());
        }
        return true;
    }

    static function sendMessageToAdmin(TelegramService $telegramService, $message)
    {
        try {
            $admin_accounts = TelegramAccount::whereHas('user', function ($q) {
                $q->where('admin', 1);
            })->get();

            foreach ($admin_accounts as $account) {
                self::sendMessage($telegramService, $account->chat_id, $message);
            }
            return true;
        } catch (\Exception $e) {
            Log::error("Error al envair el mensaje " . $e->getMessage());
            return false;
        }
    }


    static function destroyContact(TelegramService $telegramService, $chatId)
    {
        try {
            $telegramService->deleteHistory($chatId);
            $telegramService->deleteContact($chatId);
            return true;
        } catch (\Exception $e) {
            Log::error('No se ha podido eliminar el registro');
            return false;
        }

        return false;
    }
}
