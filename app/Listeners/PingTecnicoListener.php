<?php

namespace App\Listeners;

use App\Events\PingTecnicoEvent;
use App\Http\Controllers\MailerController;
use App\Models\EmailAccount;
use App\Models\MailSetting;
use App\Models\User;
use App\Notifications\PingTecnicoNotification;
use Illuminate\Support\Facades\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Config;
use App\Services\MailNotificationService;
use Illuminate\Support\Facades\Mail;
use App\Jobs\Mailing;
class PingTecnicoListener
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(PingTecnicoEvent $event): void
    {
        $mail = MailSetting::first();
        $user = User::where('id', $event->ping->user_id)->get()->first();

        Mailing::dispatch($mail,  $user, $this->createSubject($event), $this->createHTML());
        
        Notification::send($user, new PingTecnicoNotification($event->ping));
    }  
    public function createSubject($event){
        return "Revision del dispositivo no. ".$event->ping->device_id;
    }
    public function createHTML(){
        return '
    <!DOCTYPE html>
    <html>
    <body style="background-color: #f7fafc; padding: 32px;">
        <div style="max-width: 600px; margin: auto; background-color: #ffffff; border-radius: 8px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); padding: 24px;">
            <h1 style="font-size: 24px; font-weight: 700; color: #3b82f6;">¡Hola, estimado técnico!</h1>
            <p style="color: #4a5568; margin-top: 16px;">
                Se le ha asignado un dispositvo para verificar su correcto funcionamiento <strong></strong>.
            </p>
            <a href="#" style="display: inline-block; margin-top: 24px; background-color: #3b82f6; color: #ffffff; padding: 8px 16px; border-radius: 8px; text-decoration: none;">
                Ver dispositivo
            </a>
        </div>
    </body>
    </html>';
    }
}
