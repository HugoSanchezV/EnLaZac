<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Models\User;
use App\Notifications\TicketNotification;
use Illuminate\Support\Facades\Notification;
use App\Events\TicketEvent;
use App\Http\Controllers\MailerController;
use App\Jobs\Mailing;
use App\Models\EmailAccount;
use App\Models\MailSetting;
use Exception;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;

class TicketListener
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
    public function handle(TicketEvent $event): void
    {
        try{
            $settings = MailSetting::first();
            $users = User::whereIn('admin', [1, 3])->
            where('id', '!=', $event->ticket->user_id);
            
            $users->each(function (User $user) use ($event, $settings) {
        
                    if($settings){
                        Mailing::dispatch($settings, $user, $this->createSubject($event), $this->createHTML());
                    }
                    Notification::send($user, new TicketNotification($event->ticket));
        
                    // Log::error("Error al enviar notificación/correo al usuario {$user->id}: {$e->getMessage()}");
            
            });
        }catch(\Exception $e){
            throw new Exception('Error' . $e->getMessage());
        }
        
    }
    public function createSubject($event){
        return "Nuevo ticket no. ".$event->ticket->id;
    }
    public function createHTML(){
        return '
        <!DOCTYPE html>
        <html>
        <body style="background-color: #f7fafc; padding: 32px;">
            <div style="max-width: 600px; margin: auto; background-color: #ffffff; border-radius: 8px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); padding: 24px;">
                <h1 style="font-size: 24px; font-weight: 700; color: #3b82f6;">¡Hola, estimado empleado!</h1>
                <p style="color: #4a5568; margin-top: 16px;">
                    Se ha generado un nuevo ticket de un usuario.
                </p>
                <a href="#" style="display: inline-block; margin-top: 24px; background-color: #3b82f6; color: #ffffff; padding: 8px 16px; border-radius: 8px; text-decoration: none;">
                    Ver ticket
                </a>
            </div>
        </body>
        </html>';
    }
}
