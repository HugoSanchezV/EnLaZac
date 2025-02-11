<?php

namespace App\Listeners;

use App\Events\ContractWarningEvent;
use App\Jobs\Mailing;
use App\Models\EmailAccount;
use App\Models\MailSetting;
use Illuminate\Support\Facades\Config;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Notification;
use App\Models\User;
use App\Notifications\ContractWarningNotification;
use Exception;

class ContractWarningListener
{
    /**
     * Create the event listener.
     */
    public function __construct() {}

    /**
     * Handle the event.
     */
    public function handle(ContractWarningEvent  $event): void
    {
        try {
            $mail = MailSetting::first();

            $user = User::findOrFail($event->contract->inventorieDevice->device->user->user_id);

            if ($mail) {

                Mailing::dispatch($mail,  $user, $this->createSubject($event), $this->createHTML($event->days));
            }
            Notification::send($user, new ContractWarningNotification($event->contract, $event->days));
        } catch (\Exception $e) {
            throw new Exception('Error' . $e->getMessage());
        }
    }
    public function createSubject($event)
    {
        return "Pago de servicio de internet";
    }
    public function createHTML($days)
    {
        $url = route('dashboard');
        return '
    <!DOCTYPE html>
    <html>
    <body style="background-color: #f7fafc; padding: 32px;">
        <div style="max-width: 600px; margin: auto; background-color: #ffffff; border-radius: 8px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); padding: 24px;">
            <h1 style="font-size: 24px; font-weight: 700; color: #3b82f6;">¡Hola, estimado empleado!</h1>
            <p style="color: #4a5568; margin-top: 16px;">
                Se informa que solo faltan ' . $days . 'días para el día de corte, por lo que se le recomienda realizar su
                pago lo más pronto posible
            </p>
            <a href="'.$url.'" style="display: inline-block; margin-top: 24px; background-color: #3b82f6; color: #ffffff; padding: 8px 16px; border-radius: 8px; text-decoration: none;">
                Realizar pago
            </a>
        </div>
    </body>
    </html>';
    }
}
