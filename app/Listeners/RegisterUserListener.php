<?php

namespace App\Listeners;

use App\Events\RegisterUserEvent;
use App\Jobs\Mailing;
use App\Models\EmailAccount;
use App\Models\MailSetting;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Notifications\RegisterUserNotification;
use Illuminate\Support\Facades\Notification;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Config;

class RegisterUserListener
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
    public function handle(RegisterUserEvent $event): void
    {
        
        $mail = MailSetting::first();

        try{
            User::whereIn('admin', [1, 2])
            ->each(function(User $user) use ($event, $mail) {
                if($mail) {
                    Mailing::dispatch($mail,  $user, $this->createSubject($event), $this->createHTML($event->user));
                }
                Notification::send($user, new RegisterUserNotification($event->user));
            });
        }catch(\Exception $e)
        {
            throw new Exception('Error' . $e->getMessage());
        }
       
    }

    public function createSubject($event){
        return "Nuevo usuario registrado no. ".$event->user->id;
    }
    public function createHTML($usuario){
        $url = route('usuarios.show', ['user' => $usuario]);
        return '
    <!DOCTYPE html>
    <html>
    <body style="background-color: #f7fafc; padding: 32px;">
        <div style="max-width: 600px; margin: auto; background-color: #ffffff; border-radius: 8px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); padding: 24px;">
            <h1 style="font-size: 24px; font-weight: 700; color: #3b82f6;">¡Hola, estimado empleado!</h1>
            <p style="color: #4a5568; margin-top: 16px;">
                Un usuario nuevo se ha registrado en el sistemas.
            </p>
            <a href="'.$url.'" style="display: inline-block; margin-top: 24px; background-color: #3b82f6; color: #ffffff; padding: 8px 16px; border-radius: 8px; text-decoration: none;">
                Ver usuario
            </a>
        </div>
    </body>
    </html>';
    }
    
}
