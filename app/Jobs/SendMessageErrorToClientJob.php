<?php

namespace App\Jobs;

use App\Models\Device;
use App\Models\MailSetting;
use App\Models\Router;
use App\Models\TelegramAccount;
use App\Models\User;
use App\Services\TelegramService;
use App\Services\UserTelegramService;
use Exception;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class SendMessageErrorToClientJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable;

    protected $id;
    /**
     * Create a new job instance.
     */
    public function __construct($id)
    {
        //
        $this->id = $id;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        //
        try {
            $mail = MailSetting::first();

            $devices = Device::with('user')->where('router_id', $this->id);

            foreach ($devices as $device) {
                try {
                    if ($mail) {
                        Mailing::dispatch($mail,  $device->user, $this->createSubject(), $this->createHTML());
                    }
                    Log::info("Correo enviado con exito " . $device->user->phone);
                } catch (Exception $e) {
                    Log::info("Error al mandar el correo" . $device->user->phone);
                    Log::info("Error de correo " . $e->getMessage());
                }


                try {
                    if ($mail) {
                        $telegramAccount = TelegramAccount::where('user_id', $device->user->id);
                        $telegramService = new TelegramService();

                        UserTelegramService::sendMessage(
                            $telegramService,
                            $telegramAccount->chat_id,
                            "La red se encuentra en mantenimiento"
                        );
                    }
                    Log::info("Telegrama enviado con exito " . $device->user->phone);
                } catch (Exception $e) {
                    Log::info("Error al mandar el telegram" . $device->user->phone);
                    Log::info("Error de telegram " . $e->getMessage());
                }
            }
        } catch (Exception $e) {
            Log::info("Error al enviar notificaciones de error a los clientes, gracias" . $device->user->phone);
            Log::info("Error de notificaciones " . $e->getMessage());
        }
    }
    public function createSubject()
    {
        return "La red se encuentra en mantenimiento";
    }
    public function createHTML()
    {
        return '
    <!DOCTYPE html>
    <html>
    <body style="background-color: #f7fafc; padding: 32px;">
        <div style="max-width: 600px; margin: auto; background-color: #ffffff; border-radius: 8px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); padding: 24px;">
            <h1 style="font-size: 24px; font-weight: 700; color: #3b82f6;">¡Hola, estimado usuario!</h1>
            <p style="color: #4a5568; margin-top: 16px;">
              El router de su red, se encunetra en mantenimiento, gracias por la espera  <strong></strong>.
            </p>
        </div>
    </body>
    </html>';
    }
}
