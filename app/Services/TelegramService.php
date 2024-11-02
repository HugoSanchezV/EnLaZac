<?php

namespace App\Services;

use danog\MadelineProto\API;
use danog\MadelineProto\Settings;
use Illuminate\Support\Facades\Log;

class TelegramService
{
    protected $madelineProto;

    public function __construct()
    {
        // Obtener el array de configuración
        $settingsArray = config('madelineproto.settings');
        $session = config('madelineproto.session');

        try {
            // Crear una instancia de Settings
            $settings = new Settings($settingsArray);

            // Inicializar MadelineProto con la instancia de Settings
            $this->madelineProto = new API($session, $settings);
        } catch (\Exception $e) {
            Log::error('Error al inicializar MadelineProto: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Iniciar sesión en Telegram.
     */
    public function startSession()
    {
        try {
            $this->madelineProto->start();
            Log::info('Sesión de Telegram iniciada correctamente.');
        } catch (\Exception $e) {
            Log::error('Error al iniciar sesión en Telegram: ' . $e->getMessage());
            throw $e;
        }
    }

    // /**
    //  * Enviar un mensaje a un usuario o grupo.
    //  *
    //  * @param string $peer
    //  * @param string $message
    //  */
    public function importContact(string $phoneNumber, string $firstName, string $lastName = '')
    {
        try {
            $result = $this->madelineProto->contacts->importContacts([
                'contacts' => [
                    [
                        'phone' => $phoneNumber,
                        'first_name' => $firstName,
                        'last_name' => $lastName,
                    ],
                ],
            ]);

            Log::info("Contacto importado: {$firstName} {$lastName} ({$phoneNumber})");
            return $result;
        } catch (\Exception $e) {
            Log::error('Error al importar contacto: ' . $e->getMessage());
            throw $e;
        }
    }
    
    public function sendMessage(string $peer, string $message)
    {
        try {
            $this->madelineProto->messages->sendMessage([
                'peer' => $peer,
                'message' => $message,
            ]);
            Log::info("Mensaje enviado a {$peer}: {$message}");
        } catch (\Exception $e) {
            Log::error('Error al enviar mensaje: ' . $e->getMessage());
            throw $e;
        }
    }

    // Puedes agregar más métodos según tus necesidades
}
